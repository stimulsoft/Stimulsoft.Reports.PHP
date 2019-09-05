using System;
using System.Text;
using System.Xml;
using System.Collections;
using System.IO;
using System.Diagnostics;
using System.Threading;

namespace Builder
{
    class ProjectHelper
    {
        public static DialogForm Dialog = new DialogForm();
        public static string FlexSources = @"D:\Stimulsoft\Stimulsoft.Reports.Flex\";
        public static string FlexSdkPath = "sdk";
        public static string TempPath = "temp";
        public static string JavaPath = "jre";
        public static string MakePath = "make";
        public static string Version = "";
        public static DateTime DateBuild = DateTime.Now;

        private static bool createSources(Project project)
        {
            project.CompilingSrc = TempPath + "/" + project.ProjectName;

            DirectoryInfo srcDir = new DirectoryInfo(project.Src);
            DirectoryInfo destDir = new DirectoryInfo(project.CompilingSrc);

            return createSources(srcDir, destDir, project);
        }

        private static bool createSources(DirectoryInfo srcDir, DirectoryInfo destDir, Project project)
        {
            if (destDir.Exists) destDir.Delete(true);
            destDir.Create();

            foreach (FileInfo fileInfo in srcDir.GetFiles())
            {
                if (fileInfo.Extension == ".as" || fileInfo.Extension == ".mxml")
                {
                    StreamReader reader = new StreamReader(fileInfo.Open(FileMode.Open));
                    string file = reader.ReadToEnd();
                    reader.Close();

                    file = file.Replace("//[ExcludeClass]", "[ExcludeClass]");

                    if (fileInfo.Extension == ".mxml") file = file.Replace("Theme.isTheme2013 = true;//debug", "//Theme.isTheme2013 = true;//debug");
                    
                    if (fileInfo.Name == "StiVersion.as" && fileInfo.DirectoryName.EndsWith("stimulsoft\\base"))
                    {
                        string dateTime = DateBuild.ToString("yyyy-MM-dd hh:mm:ss");
                        file = file.Replace("#date", dateTime);
                        file = file.Replace("#version", Version);
                    }

                    if (project.MainClass != "" &&  fileInfo.Name == project.MainClass) file = file.Replace("private var isRelease: Boolean = false;", "private var isRelease: Boolean = true;");

                    FileInfo newFileInfo = new FileInfo(Path.Combine(destDir.FullName, fileInfo.Name));
                    if (newFileInfo.Exists) newFileInfo.Delete();
                    StreamWriter writer = new StreamWriter(newFileInfo.OpenWrite());
                    writer.Write(file);
                    writer.Close();
                }
                else
                {
                    FileInfo newFileInfo = new FileInfo(Path.Combine(destDir.FullName, fileInfo.Name));
                    fileInfo.CopyTo(newFileInfo.FullName);
                }
            }

            foreach (DirectoryInfo subSrcDir in srcDir.GetDirectories())
            {
                DirectoryInfo mainDir = new DirectoryInfo(project.Src);
                if (subSrcDir.Attributes != (FileAttributes.Hidden | FileAttributes.Directory) && subSrcDir.Name != ".svn" && subSrcDir.Name != ".settings")
                {
                    DirectoryInfo subDestDir = destDir.CreateSubdirectory(subSrcDir.Name);
                    createSources(subSrcDir, subDestDir, project);
                }
                if (mainDir.FullName == subSrcDir.Parent.FullName + "\\")
                {
                    if (project.Packed.IndexOf(subSrcDir.Name) != -1)
                    {
                        DirectoryInfo subDestDir = destDir.CreateSubdirectory(subSrcDir.Name);
                        copyDirectory(subSrcDir.FullName, subDestDir.FullName);
                    }
                }
            }

            return true;
        }

        private static void createConfigAir(Project project)
        {
            FileInfo fileConfig = new FileInfo(project.CompilingSrc + "/" + project.MainClass.Substring(0, project.MainClass.Length - 5).ToLower() + "-app.xml");

            if (project.Type == ProjectType.Air)
            {
                XmlDocument config = new XmlDocument();
                config.Load(fileConfig.FullName);

                foreach (XmlNode node in config.ChildNodes[1].ChildNodes)
                {
                    string[] values = Version.Split('.');
                    string versionNumber = string.Format("{0}.{1}.{2}",
                        values.Length > 0 ? int.Parse(values[0]) - 2000 : 1,
                        values.Length > 1 ? int.Parse(values[1]) : 1,
                        values.Length > 2 ? int.Parse(values[2]) : 1
                    );

                    if (node.Name == "filename") node.InnerText = project.Name;
                    if (node.Name == "name") node.InnerText = project.Name;
                    if (node.Name == "versionNumber") node.InnerText = versionNumber;
                    if (node.Name == "versionLabel") node.InnerText = Version;
                    if (node.Name == "copyright") node.InnerText = "Stimulsoft, 2003-" + DateTime.Today.Year.ToString();
                    if (node.Name == "initialWindow")
                    {
                        foreach (XmlNode nodeInitialWindow in node.ChildNodes)
                        {
                            if (nodeInitialWindow.Name == "content")
                            {
                                nodeInitialWindow.InnerText = project.Output;
                            }
                        }
                    }
                    if (node.Name == "icon")
                    {
                        foreach (XmlNode nodeIcon in node.ChildNodes)
                        {
                            project.Icons.Add(nodeIcon.InnerText);
                            string[] list = nodeIcon.InnerText.Split('/');
                            nodeIcon.InnerText = "icons/" + list[list.Length - 1];
                            project.Icons.Add(nodeIcon.InnerText);
                        }
                    }
                    if (node.Name == "fileTypes")
                    {
                        foreach (XmlNode nodeFileType in node.ChildNodes)
                        {
                            if (nodeFileType.Name == "fileType")
                            {
                                foreach (XmlNode nodeFileTypeIcon in nodeFileType.ChildNodes)
                                {
                                    if (nodeFileTypeIcon.Name == "icon")
                                    {
                                        foreach (XmlNode nodeIcon in nodeFileTypeIcon.ChildNodes)
                                        {
                                            project.Icons.Add(nodeIcon.InnerText);
                                            string[] list = nodeIcon.InnerText.Split('/');
                                            nodeIcon.InnerText = "icons/" + list[list.Length - 1];
                                            project.Icons.Add(nodeIcon.InnerText);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                config.Save(project.CompilingSrc + "/" + fileConfig.Name);
            }
        }

        private static void createManifestFile(string path)
        {
            string str = string.Format(
                "Manifest-Version: 1.0\n" +
                "Built-By: Stimulsoft Project Builder\n" +
                "Build-Jdk: 1.5.0_16\n" +
                "Archiver-Version: 7-Zip 9.20\n" +
                "Implementation-Title: Reports.Fx for Java\n" +
                "Implementation-Vendor: Stimulsoft\n" +
                "Implementation-Version: {0}\n",
                Version);

            path = Path.Combine(path, "META-INF");
            Directory.CreateDirectory(path);

            FileStream stream = new FileStream(Path.Combine(path, "MANIFEST.MF"), FileMode.Create);
            byte[] bytes = Encoding.UTF8.GetBytes(str);
            stream.Write(bytes, 0, bytes.Length);
            stream.Close();
        }
        
        public static void copyDirectory(string fromDirectory, string toDirectory)
        {
            Directory.CreateDirectory(toDirectory);
            foreach (string s1 in Directory.GetFiles(fromDirectory))
            {
                string s2 = toDirectory + "\\" + Path.GetFileName(s1);
                File.Copy(s1, s2, true);
            }
            foreach (string s in Directory.GetDirectories(fromDirectory))
            {
                if (!s.EndsWith(".svn"))
                {
                    copyDirectory(s, toDirectory + "\\" + Path.GetFileName(s));
                }
            }
        }

        private static ArrayList getClasses(Project project)
        {
            return getClasses(new DirectoryInfo(project.CompilingSrc), "");
        }

        private static ArrayList getClasses(DirectoryInfo dirInfo, string packages)
        {
            ArrayList classes = new ArrayList();
            foreach (FileInfo file in dirInfo.GetFiles())
            {
                if (file.Extension == ".as") classes.Add(packages + file.Name.Substring(0, file.Name.Length - 3));
                else if (file.Extension == ".mxml") classes.Add(packages + file.Name.Substring(0, file.Name.Length - 5));
            }

            foreach (DirectoryInfo innerDirInfo in dirInfo.GetDirectories())
            {
                if (innerDirInfo.Attributes == (FileAttributes.Hidden | FileAttributes.Directory)) continue;

                classes.AddRange(getClasses(innerDirInfo, packages + innerDirInfo.Name + "."));
            }

            return classes;
        }

        private static string parseBoolToString(bool value)
        {
            if (value == true) return "true";
            return "false";
        }

        private static XmlDocument createConfigXml(Project project)
        {
            XmlDocument config = new XmlDocument();

            XmlElement flexConfigNode = config.CreateElement("flex-config");
            XmlElement compilerNode = config.CreateElement("compiler");
            XmlElement externalLibraryPathNode = config.CreateElement("external-library-path");
            XmlElement libraryPathNode = config.CreateElement("library-path");
            XmlElement frameworkNode = externalLibraryPathNode;
            if (project.Type == ProjectType.Swf || project.Type == ProjectType.Air) frameworkNode = libraryPathNode;

            #region Debug
            XmlElement debugNode = config.CreateElement("debug");
            debugNode.InnerText = project.IsDebug.ToString();
            #endregion

            #region Trial
            XmlElement defineTrialNode = config.CreateElement("define");
            XmlElement trialNameNode = config.CreateElement("name");
            XmlElement trialValueNode = config.CreateElement("value");
            trialNameNode.InnerText = "CONFIG::Trial";
            trialValueNode.InnerText = parseBoolToString(project.IsTrial);
            #endregion

            #region Release
            XmlElement defineReleaseNode = config.CreateElement("define");
            XmlElement releaseNameNode = config.CreateElement("name");
            XmlElement releaseValueNode = config.CreateElement("value");
            releaseNameNode.InnerText = "CONFIG::Release";
            releaseValueNode.InnerText = parseBoolToString(!project.IsTrial && !project.IsWeb && !project.IsJava);
            #endregion

            #region Web
            XmlElement defineWebNode = config.CreateElement("define");
            XmlElement webNameNode = config.CreateElement("name");
            XmlElement webValueNode = config.CreateElement("value");
            webNameNode.InnerText = "CONFIG::Web";
            webValueNode.InnerText = parseBoolToString(project.IsWeb || project.IsJava);
            #endregion

            XmlElement pathElementNode;
            XmlElement outputNode = config.CreateElement("output");
            XmlElement sourcePathNode = config.CreateElement("source-path");
            XmlElement sourcePathElementNode = config.CreateElement("path-element");

            #region Frameworks swc
            pathElementNode = config.CreateElement("path-element");
            frameworkNode.AppendChild(pathElementNode);
            pathElementNode.InnerText = String.Format("../{0}/{1}", FlexSdkPath, "frameworks/libs");

            pathElementNode = config.CreateElement("path-element");
            frameworkNode.AppendChild(pathElementNode);
            pathElementNode.InnerText = String.Format("../{0}/{1}", FlexSdkPath, "frameworks/libs/mx");

            if (project.Type == ProjectType.SwcAir || project.Type == ProjectType.Air)
            {
                pathElementNode = config.CreateElement("path-element");
                frameworkNode.AppendChild(pathElementNode);
                pathElementNode.InnerText = String.Format("../{0}/{1}", FlexSdkPath, "frameworks/libs/air");
            }
            else
            {
                pathElementNode = config.CreateElement("path-element");
                frameworkNode.AppendChild(pathElementNode);
                pathElementNode.InnerText = String.Format("../{0}/{1}", FlexSdkPath, "frameworks/libs/player");

                pathElementNode = config.CreateElement("path-element");
                frameworkNode.AppendChild(pathElementNode);
                pathElementNode.InnerText = String.Format("../{0}/{1}", FlexSdkPath, "frameworks/libs/player/11.1");
            }

            pathElementNode = config.CreateElement("path-element");
            frameworkNode.AppendChild(pathElementNode);
            pathElementNode.InnerText = String.Format("../{0}/{1}", FlexSdkPath, "frameworks/locale/en_US");
            #endregion

            #region Library
            foreach (Project dependsProject in project.DependsOn)
            {
                XmlElement pathDependsElementNode = config.CreateElement("path-element");

                if (project.Appends[dependsProject.ProjectName] != null) libraryPathNode.AppendChild(pathDependsElementNode);
                else externalLibraryPathNode.AppendChild(pathDependsElementNode);

                pathDependsElementNode.InnerText = dependsProject.Output;
            }
            #endregion

            outputNode.InnerText = project.Output;
            sourcePathElementNode.InnerText = "../" + project.CompilingSrc;

            #region InnerClass
            if (project.Type == ProjectType.Swc || project.Type == ProjectType.SwcAir)
            {
                XmlElement includeClassesNode = config.CreateElement("include-classes");
                flexConfigNode.AppendChild(includeClassesNode);

                foreach (string classPath in getClasses(project))
                {
                    XmlElement classNode = config.CreateElement("class");
                    includeClassesNode.AppendChild(classNode);

                    classNode.InnerText = classPath;
                }
            }
            #endregion

            XmlElement root = config.DocumentElement;
            config.InsertBefore(config.CreateXmlDeclaration("1.0", null, null), root);

            config.AppendChild(flexConfigNode);
            flexConfigNode.AppendChild(compilerNode);
            compilerNode.AppendChild(externalLibraryPathNode);
            compilerNode.AppendChild(libraryPathNode);
            compilerNode.AppendChild(debugNode);

            compilerNode.AppendChild(defineTrialNode);
            defineTrialNode.AppendChild(trialNameNode);
            defineTrialNode.AppendChild(trialValueNode);

            compilerNode.AppendChild(defineReleaseNode);
            defineReleaseNode.AppendChild(releaseNameNode);
            defineReleaseNode.AppendChild(releaseValueNode);

            compilerNode.AppendChild(defineWebNode);
            defineWebNode.AppendChild(webNameNode);
            defineWebNode.AppendChild(webValueNode);

            flexConfigNode.AppendChild(outputNode);
            compilerNode.AppendChild(sourcePathNode);
            sourcePathNode.AppendChild(sourcePathElementNode);

            return config;
        }

        private static string createCompileArgument(Project project)
        {
            return createCompileArgument(project, false);
        }

        private static string createCompileArgument(Project project, bool packAir)
        {
            var argument = "";
            if (packAir)
            {
                argument = String.Format(
                    //"-XX:MaxPermSize=256m -Xmx768m -XX:+UseParallelGC -jar {0}/lib/adt.jar -package -storetype pkcs12 -keystore Stimulsoft.p12 -storepass 112 {1}/{2} {3}/{4}-app.xml -C {1} {5}",
                    //"-XX:MaxPermSize=256m -Xmx768m -XX:+UseParallelGC -jar {0}/lib/adt.jar -package -storetype pkcs12 -keystore StimulsoftCertificate.pfx -storepass Vandals1002 -tsa none {1}/{2} {3}/{4}-app.xml -C {1} {5}",
                    //"-XX:MaxPermSize=256m -Xmx768m -XX:+UseParallelGC -jar {0}/lib/adt.jar -package -storetype pkcs12 -keystore StimulsoftCertificate.pfx -storepass Vandals1002 {1}/{2} {3}/{4}-app.xml -C {1} {5}",
                    //"-XX:MaxPermSize=256m -Xmx768m -XX:+UseParallelGC -jar {0}/lib/adt.jar -package -storetype pkcs12 -keystore StimulsoftCertificate.pfx -storepass Vandals1002 -tsa http://timestamp.digicert.com {1}/{2} {3}/{4}-app.xml -C {1} {5}",
                    //"-XX:MaxPermSize=256m -Xmx768m -XX:+UseParallelGC -jar {0}/lib/adt.jar -package -storetype pkcs12 -keystore d:\\Stimulsoft\\StimulsoftCertificate.p12 -storepass Vandals1002 -tsa http://timestamp.digicert.com {1}/{2} {3}/{4}-app.xml -C {1} {5}",
                    "-XX:MaxPermSize=256m -Xmx768m -XX:+UseParallelGC -jar {0}/lib/adt.jar -package -storetype pkcs12 -keystore d:\\Stimulsoft\\Sign\\StimulsoftCertificate.pfx -storepass Vandals1002 -tsa http://timestamp.digicert.com {1}/{2} {3}/{4}-app.xml -C {1} {5}",
                    FlexSdkPath, TempPath, project.ProjectNameOutput + ".air", project.CompilingSrc, project.MainClass.Substring(0, project.MainClass.Length - 5), project.Output);

                foreach (String packed in project.Packed)
                {
                    argument += " -C " + project.CompilingSrc + " " + packed;
                }
            }

            if (project.Type == ProjectType.Swc || project.Type == ProjectType.SwcAir)
                argument = string.Format(
                    "-XX:MaxPermSize=256m -Xmx768m -XX:+UseParallelGC -jar {0}/lib/compc.jar -load-config=flex-config.xml -load-config+={1}/{2}-config.xml -local-fonts-snapshot={0}/frameworks/localFonts.ser",
                    FlexSdkPath, TempPath, project.ProjectNameOutput);
            
            if (project.Type == ProjectType.Swf)
                argument = string.Format(
                    "-XX:MaxPermSize=256m -Xmx768m -XX:+UseParallelGC -jar {0}/lib/mxmlc.jar -load-config=flex-config.xml -load-config+={1}/{2}-config.xml -local-fonts-snapshot={0}/frameworks/localFonts.ser {3}/{4}",
                    FlexSdkPath, TempPath, project.ProjectNameOutput, project.CompilingSrc, project.MainClass);

            if (project.Type == ProjectType.Air && !packAir)
                argument = string.Format(
                    "-XX:MaxPermSize=256m -Xmx768m -XX:+UseParallelGC -jar {0}/lib/mxmlc.jar +configname=air -load-config=flex-config.xml -load-config+={1}/{2}-config.xml -local-fonts-snapshot={0}/frameworks/localFonts.ser {3}/{4}",
                    FlexSdkPath, TempPath, project.ProjectNameOutput, project.CompilingSrc, project.MainClass);

            return argument;
        }

        private static Process process;
        private static bool endProcess = false;
        private static StringBuilder error = new StringBuilder();
        private static void startCurrentProcess()
        {
            error = new StringBuilder();
            endProcess = false;
            process.Start();

            if (process != null && !process.HasExited)
            {
                while (!process.WaitForExit(0))
                {
                    process.Refresh();
                    error.Append(process.StandardError.ReadToEnd());
                }
            }
            endProcess = true;
        }

        public static bool compile(Project project, bool copySources, bool isTrial, bool isDebug, bool isWeb, bool isAir, bool isJava)
        {
            DateTime timeStart = DateTime.Now;

            project.IsTrial = isTrial;
            project.IsDebug = isDebug;
            project.IsWeb = isWeb;
            project.IsJava = isJava;

            DirectoryInfo tempDir = new DirectoryInfo(TempPath);
            if (isAir)
            {
                Dialog.Append(project.ProjectNameOutput + ".air");
                Thread.Sleep(50);

                createConfigAir(project);
                DirectoryInfo iconsDir = new DirectoryInfo(project.CompilingSrc + "/" + "icons");

                if (!iconsDir.Exists)
                {
                    iconsDir.Create();

                    for (int index = 0; index < project.Icons.Count; index += 2)
                    {
                        FileInfo fileInfo = new FileInfo(project.CompilingSrc + "/" + project.Icons[index]);
                        FileInfo newFileInfo = new FileInfo(project.CompilingSrc + "/" + project.Icons[index + 1]);
                        fileInfo.CopyTo(newFileInfo.FullName, true);
                    }
                }
            }
            else
            {
                Dialog.Append(project.Output);

                tempDir.Create();

                if (copySources) createSources(project);
                else Thread.Sleep(1000);
                
                XmlDocument config = createConfigXml(project);
                config.Save(TempPath + "\\" + project.ProjectNameOutput + "-config.xml");
            }

            ProcessStartInfo java = new ProcessStartInfo();
            java.UseShellExecute = false;
            java.RedirectStandardOutput = true;
            java.RedirectStandardError = true;
            java.FileName = string.Format("{0}\\bin\\java.exe", JavaPath);
            java.Arguments = createCompileArgument(project, isAir);
            java.StandardOutputEncoding = Encoding.GetEncoding(866);
            java.CreateNoWindow = true;

            process = new Process();
            process.StartInfo = java;

            endProcess = false;

            Thread thread = new Thread(new ThreadStart(startCurrentProcess));
            thread.Start();

            while (!endProcess)
            {
                Dialog.Append(".");
                Thread.Sleep(250);
                if (process.StandardOutput != null) process.StandardOutput.Close();
            }

            if (error.Length <= 0)
            {
                while (!process.StandardError.EndOfStream)
                {
                    error.Append(((char)process.StandardError.Read()).ToString());
                }
            }

            process.Close();
            process.Dispose();
            process = null;

            thread.Abort();
            thread = null;

            if (error.Length > 0)
            {
                Dialog.AppendLine("Error");
                Dialog.AppendLine("");
                Dialog.Append(error.ToString());

                Dialog.flashForm();

                return false;
            }
            else
            {
                Dialog.AppendLine(" " + new DateTime((DateTime.Now - timeStart).Ticks).ToString("mm:ss"));
            }

            return true;
        }
        
        public static bool archive(string directory, string project, bool createTextFile)
        {
            Dialog.Append(project);
            Thread.Sleep(300);

            DateTime timeStart = DateTime.Now;

            while (directory.EndsWith("\\")) directory = directory.Substring(0, directory.Length - 1);
            if (createTextFile) File.WriteAllText(directory + ".txt", Version);

            ProcessStartInfo zip = new ProcessStartInfo();
            zip.UseShellExecute = false;
            zip.RedirectStandardOutput = true;
            zip.RedirectStandardError = true;
            zip.FileName = "7z.exe";
            zip.Arguments = string.Format("a -tzip -mx=7 \"{0}.zip\" \"{0}\"", directory, directory);
            zip.StandardOutputEncoding = Encoding.GetEncoding(866);
            zip.CreateNoWindow = true;

            Thread.Sleep(300);
            process = new Process();
            process.StartInfo = zip;

            endProcess = false;

            Thread.Sleep(300);
            Thread thread = new Thread(new ThreadStart(startCurrentProcess));
            thread.Start();
            
            while (!endProcess)
            {
                Dialog.Append(".");
                Thread.Sleep(250);

                if (process.StandardOutput != null) process.StandardOutput.Close();
            }

            if (error.Length <= 0)
            {
                while (!process.StandardError.EndOfStream)
                {
                    error.Append(((char)process.StandardError.Read()).ToString());
                }
            }

            Thread.Sleep(300);

            process.Close();
            process.Dispose();
            process = null;

            thread.Abort();
            thread = null;

            if (error.Length > 0)
            {
                Dialog.AppendLine("Error: " + error.ToString());
                Dialog.AppendLine("");

                Dialog.flashForm();

                return false;
            }
            else
            {
                Dialog.AppendLine(" " + new DateTime((DateTime.Now - timeStart).Ticks).ToString("mm:ss"));
            }

            return true;
        }

        public static void replaceVersion(string filePath)
        {
            StreamReader reader = new StreamReader(File.Open(filePath, FileMode.Open));
            string file = reader.ReadToEnd();
            reader.Close();

            file = file.Replace("#MARKER_BUILD#", Version);

            FileInfo newFileInfo = new FileInfo(filePath);
            if (newFileInfo.Exists) newFileInfo.Delete();
            StreamWriter writer = new StreamWriter(newFileInfo.OpenWrite());
            writer.Write(file);
            writer.Close();
        }

        public static void copyFlexSources(string pathSources, string projectName)
        {
            var projectFlex = new string[] {
                "ApiProvider_AIR", "ApiProvider_Flex", "DemoFx_AIR", "DemoFx_Flex", "DesignerFx_AIR", "ExportsFx",
                "_Base", "_ControlsFx", "_Database", "_DesignerFx", "_DesignerFx_Images", "_DesignerFx_Images2013", "_Preloader", "_PropertyGrid", "_Report", "_Report_Check", "_ViewerFx"
            };
            var projectPhp = new string[] {
                "ApiProvider_AIR", "ApiProvider_Flex", "ApiProvider_PHP", "DemoFx_AIR", "DesignerFx_AIR", "DesignerFx_PHP", "ExportsFx", "ViewerFx_PHP",
                "_Base", "_ControlsFx", "_Database", "_DesignerFx", "_DesignerFx_Images", "_DesignerFx_Images2013", "_Preloader", "_PropertyGrid", "_Report", "_Report_Check", "_ViewerFx"
            };
            var projectJava = new string[] {
                "ApiProvider_AIR", "ApiProvider_Flex", "ApiProvider_Java", "DemoFx_AIR", "DesignerFx_AIR", "DesignerFx_Java", "ViewerFx_Java",
                "_Base", "_ControlsFx", "_Database", "_DesignerFx", "_DesignerFx_Images", "_DesignerFx_Images2013", "_Preloader", "_PropertyGrid", "_Report", "_Report_Check", "_ViewerFx"
            };
            var projectWeb = new string[] {
                "ApiProvider_Flex", "ApiProvider_Web", "DesignerFx_Web", "ViewerFx_Javascript", "ViewerFx_Web",
                "_Base", "_ControlsFx", "_Database", "_DesignerFx", "_DesignerFx_Images", "_DesignerFx_Images2013", "_Preloader", "_PropertyGrid", "_Report", "_Report_Check", "_ViewerFx"
            };

            var project = new string[] { };
            switch (projectName)
            {
                case "Flex": project = projectFlex; break;
                case "PHP-Flex": project = projectPhp; break;
                case "Java-Flex": project = projectJava; break;
                case "Web-Flex": project = projectWeb; break;
            }

            pathSources = Path.Combine(pathSources, projectName);
            Directory.CreateDirectory(pathSources);

            foreach (string name in project)
            {
                string pathProject = Path.Combine(ProjectHelper.FlexSources, name.StartsWith("_") ? "Stimulsoft" + name : name);
                string pathSourcesProject = Path.Combine(pathSources, name.StartsWith("_") ? "Stimulsoft" + name : name);
                Directory.CreateDirectory(pathSourcesProject);

                File.Copy(Path.Combine(pathProject, ".actionScriptProperties"), Path.Combine(pathSourcesProject, ".actionScriptProperties"), true);
                File.Copy(Path.Combine(pathProject, ".project"), Path.Combine(pathSourcesProject, ".project"), true);
                if (File.Exists(Path.Combine(pathProject, ".flexLibProperties"))) File.Copy(Path.Combine(pathProject, ".flexLibProperties"), Path.Combine(pathSourcesProject, ".flexLibProperties"), true);
                if (File.Exists(Path.Combine(pathProject, ".flexProperties"))) File.Copy(Path.Combine(pathProject, ".flexProperties"), Path.Combine(pathSourcesProject, ".flexProperties"), true);
                copyDirectory(Path.Combine(pathProject, ".settings"), Path.Combine(pathSourcesProject, ".settings"));
                copyDirectory(Path.Combine(pathProject, "src"), Path.Combine(pathSourcesProject, "src"));
            }
        }
    }
}
