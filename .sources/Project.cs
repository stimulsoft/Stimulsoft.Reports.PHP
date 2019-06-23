using System;
using System.Text;
using System.Windows.Forms;
using System.Collections;

namespace Builder
{
    enum ProjectType
    {
        Swc,
        Swf,
        SwcAir,
        Air
    }

    enum ApplicationType
    {
        Flex,
        PHP,
        Java,
        Web
    }

    class Project
    {
        #region Properties
        private string projectName = "";
        public string ProjectName
        {
            get
            {
                return projectName;
            }
            set
            {
                projectName = value;
            }
        }
        public string ProjectNameOutput
        {
            get
            {
                return projectName + (IsDebug ? "_Debug" : "");
            }
        }
        
        private string name = "";
        public string Name
        {
            get
            {
                return name;
            }
            set
            {
                name = value;
            }
        }

        private string mainClass = "";
        public string MainClass
        {
            get
            {
                return mainClass;
            }
            set
            {
                mainClass = value;
            }
        }

        private ProjectType type;
        public ProjectType Type
        {
            get
            {
                return type;
            }
            set
            {
                type = value;
            }
        }

        private ApplicationType applicationType = ApplicationType.Flex;
        public ApplicationType ApplicationType
        {
            get
            {
                return applicationType;
            }
            set
            {
                applicationType = value;
            }
        }

        private string src = "";
        public string Src
        {
            get
            {
                return src;
            }
            set
            {
                src = value;
            }
        }

        private string compilingSrc = "";
        public string CompilingSrc
        {
            get
            {
                return compilingSrc;
            }
            set
            {
                compilingSrc = value;
            }
        }

        private string output = "";
        public string Output
        {
            get
            {
                if (output == "ViewerFx_JavaScript.swf") return output;

                string _output = "";
                if (IsWeb) _output += "_Web";
                else if (IsJava) _output += "_Java";
                else if (IsDebug) _output += "_Debug";

                return output.Insert(output.LastIndexOf('.'), _output);
            }
            set
            {
                output = value;
            }
        }

        private ProjectCollection dependsOn;
        public ProjectCollection DependsOn
        {
            get
            {
                return dependsOn;
            }
            set
            {
                dependsOn = value;
            }
        }

        private ProjectCollection appends;
        public ProjectCollection Appends
        {
            get
            {
                return appends;
            }
            set
            {
                appends = value;
            }
        }

        private ProjectCollection usedBy;
        public ProjectCollection UsedBy
        {
            get
            {
                return usedBy;
            }
            set
            {
                usedBy = value;
            }
        }

        private ArrayList icons = new ArrayList();
        public ArrayList Icons
        {
            get
            {
                return icons;
            }
            set
            {
                icons = value;
            }
        }

        private ArrayList packed = new ArrayList();
        public ArrayList Packed
        {
            get
            {
                return packed;
            }
            set
            {
                packed = value;
            }
        }

        private bool selected = false;
        public bool Selected
        {
            get
            {
                return selected;
            }
            set
            {
                selected = value;

                if (selected == true)
                {
                    foreach (Project project in dependsOn)
                    {
                        project.Selected = true;
                    }
                }
                else
                {
                    StringBuilder usedProject = new StringBuilder();
                    foreach (Project project in usedBy)
                    {
                        if (project.Selected == true)
                        {
                            usedProject.AppendLine(project.ProjectName);
                        }
                    }

                    if (usedProject.Length > 0) MessageBox.Show("This project used by \n\n" + usedProject.ToString());
                }
            }
        }

        private bool compiling = false;
        public bool Compiling
        {
            get
            {
                return compiling;
            }
            set
            {
                compiling = value;
            }
        }

        private bool isDebug = false;
        public bool IsDebug
        {
            get
            {
                return isDebug;
            }
            set
            {
                isDebug = value;

                foreach (Project project in dependsOn)
                {
                    project.IsDebug = isDebug;
                }
            }
        }

        private bool isTrial = false;
        public bool IsTrial
        {
            get
            {
                return isTrial;
            }
            set
            {
                isTrial = value;

                foreach (Project project in dependsOn)
                {
                    project.IsTrial = isTrial;
                }
            }
        }

        private bool isJava = false;
        public bool IsJava
        {
            get
            {
                return isJava;
            }
            set
            {
                isJava = value;

                foreach (Project project in dependsOn)
                {
                    project.IsJava = IsJava;
                }
            }
        }

        private bool isWeb = false;
        public bool IsWeb
        {
            get
            {
                return isWeb;
            }
            set
            {
                isWeb = value;

                foreach (Project project in dependsOn)
                {
                    project.IsWeb = isWeb;
                }
            }
        }
        
        #endregion

        #region Constructors
        public Project(String projectName, ProjectType type, String src, String output)
        {
            dependsOn = new ProjectCollection(this);
            appends = new ProjectCollection(this);
            usedBy = new ProjectCollection(this);

            ProjectName = projectName;
            Type = type;
            Src = src;
            Output = output;
        }
        #endregion
    }
}