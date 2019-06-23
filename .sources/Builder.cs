using System;
using System.Windows.Forms;
using System.IO;
using System.Xml;
using System.Threading;
using System.Collections;

namespace Builder
{
    public partial class Builder : Form
    {
        DirectoryInfo tempDirectory = new DirectoryInfo(ProjectHelper.TempPath);
        DirectoryInfo makeDirectory = new DirectoryInfo(ProjectHelper.MakePath);
        ProjectCollection projects;
        Hashtable checkBoxesState = new Hashtable();
        
        private void createSheme()
        {
            projects = new ProjectCollection();

            #region Flex Components

            Project projectStimulsoftBase = new Project("Stimulsoft_Base", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "Stimulsoft_Base\\src"), "Stimulsoft_Base.swc");
            projects.addProject(projectStimulsoftBase);

            Project projectStimulsoftPreloader = new Project("Stimulsoft_Preloader", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "Stimulsoft_Preloader\\src"), "Stimulsoft_Preloader.swc");
            projects.addProject(projectStimulsoftPreloader);

            Project projectStimulsoftDatabase = new Project("Stimulsoft_Database", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "Stimulsoft_Database\\src"), "Stimulsoft_Database.swc");
            projectStimulsoftDatabase.DependsOn.addProject(projectStimulsoftBase);
            projects.addProject(projectStimulsoftDatabase);

            Project projectStimulsoftControlsFx = new Project("Stimulsoft_ControlsFx", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "Stimulsoft_ControlsFx\\src"), "Stimulsoft_ControlsFx.swc");
            projectStimulsoftControlsFx.DependsOn.addProject(projectStimulsoftBase);
            projectStimulsoftControlsFx.DependsOn.addProject(projectStimulsoftPreloader);
            projects.addProject(projectStimulsoftControlsFx);

            Project projectStimulsoftReport = new Project("Stimulsoft_Report", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "Stimulsoft_Report\\src"), "Stimulsoft_Report.swc");
            projectStimulsoftReport.DependsOn.addProject(projectStimulsoftBase);
            projectStimulsoftReport.DependsOn.addProject(projectStimulsoftPreloader);
            projectStimulsoftReport.DependsOn.addProject(projectStimulsoftDatabase);
            projectStimulsoftReport.DependsOn.addProject(projectStimulsoftControlsFx);
            projects.addProject(projectStimulsoftReport);

            Project projectStimulsoftReportCheck = new Project("Stimulsoft_Report_Check", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "Stimulsoft_Report_Check\\src"), "Stimulsoft_Report_Check.swc");
            projectStimulsoftReportCheck.DependsOn.addProject(projectStimulsoftBase);
            projectStimulsoftReportCheck.DependsOn.addProject(projectStimulsoftPreloader);
            projectStimulsoftReportCheck.DependsOn.addProject(projectStimulsoftDatabase);
            projectStimulsoftReportCheck.DependsOn.addProject(projectStimulsoftControlsFx);
            projectStimulsoftReportCheck.DependsOn.addProject(projectStimulsoftReport);
            projects.addProject(projectStimulsoftReportCheck);

            Project projectStimulsoftPropertyGrid = new Project("Stimulsoft_PropertyGrid", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "Stimulsoft_PropertyGrid\\src"), "Stimulsoft_PropertyGrid.swc");
            projectStimulsoftPropertyGrid.DependsOn.addProject(projectStimulsoftBase);
            projectStimulsoftPropertyGrid.DependsOn.addProject(projectStimulsoftControlsFx);
            projects.addProject(projectStimulsoftPropertyGrid);

            Project projectStimulsoftDesignerFxImages = new Project("Stimulsoft_DesignerFx_Images", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "Stimulsoft_DesignerFx_Images\\src"), "Stimulsoft_DesignerFx_Images.swc");
            projects.addProject(projectStimulsoftDesignerFxImages);

            Project projectStimulsoftDesignerFxImages2013 = new Project("Stimulsoft_DesignerFx_Images2013", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "Stimulsoft_DesignerFx_Images2013\\src"), "Stimulsoft_DesignerFx_Images2013.swc");
            projectStimulsoftDesignerFxImages2013.DependsOn.addProject(projectStimulsoftDesignerFxImages);
            projects.addProject(projectStimulsoftDesignerFxImages2013);

            Project projectApiProviderFlex = new Project("ApiProvider_Flex", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "ApiProvider_Flex\\src"), "ApiProvider_Flex.swc");
            projectApiProviderFlex.DependsOn.addProject(projectStimulsoftBase);
            projectApiProviderFlex.DependsOn.addProject(projectStimulsoftDatabase);
            projects.addProject(projectApiProviderFlex);

            Project projectApiProviderAir = new Project("ApiProvider_AIR", ProjectType.SwcAir, Path.Combine(ProjectHelper.FlexSources, "ApiProvider_AIR\\src"), "ApiProvider_AIR.swc");
            projectApiProviderAir.DependsOn.addProject(projectStimulsoftBase);
            projectApiProviderAir.DependsOn.addProject(projectStimulsoftDatabase);
            projects.addProject(projectApiProviderAir);

            Project projectApiProviderWeb = new Project("ApiProvider_Web", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "ApiProvider_Web\\src"), "ApiProvider_Web.swc");
            projectApiProviderWeb.DependsOn.addProject(projectStimulsoftBase);
            projectApiProviderWeb.ApplicationType = ApplicationType.Web;
            projects.addProject(projectApiProviderWeb);

            Project projectApiProviderPhp = new Project("ApiProvider_PHP", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "ApiProvider_PHP\\src"), "ApiProvider_PHP.swc");
            projectApiProviderPhp.DependsOn.addProject(projectStimulsoftBase);
            projectApiProviderPhp.DependsOn.addProject(projectStimulsoftDatabase);
            projectApiProviderPhp.ApplicationType = ApplicationType.PHP;
            projects.addProject(projectApiProviderPhp);

            Project projectApiProviderJava = new Project("ApiProvider_Java", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "ApiProvider_Java\\src"), "ApiProvider_Java.swc");
            projectApiProviderJava.DependsOn.addProject(projectStimulsoftBase);
            projectApiProviderJava.DependsOn.addProject(projectStimulsoftDatabase);
            projectApiProviderJava.ApplicationType = ApplicationType.Java;
            projects.addProject(projectApiProviderJava);

            Project projectStimulsoftViewerFx = new Project("Stimulsoft_ViewerFx", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "Stimulsoft_ViewerFx\\src"), "Stimulsoft_ViewerFx.swc");
            projectStimulsoftViewerFx.DependsOn.addProject(projectStimulsoftBase);
            projectStimulsoftViewerFx.DependsOn.addProject(projectStimulsoftDatabase);
            projectStimulsoftViewerFx.DependsOn.addProject(projectStimulsoftReport);
            projectStimulsoftViewerFx.DependsOn.addProject(projectStimulsoftPreloader);
            projectStimulsoftViewerFx.DependsOn.addProject(projectStimulsoftControlsFx);
            projectStimulsoftViewerFx.DependsOn.addProject(projectApiProviderFlex);
            projects.addProject(projectStimulsoftViewerFx);

            Project projectStimulsoftDesignerFx = new Project("Stimulsoft_DesignerFx", ProjectType.Swc, Path.Combine(ProjectHelper.FlexSources, "Stimulsoft_DesignerFx\\src"), "Stimulsoft_DesignerFx.swc");
            projectStimulsoftDesignerFx.DependsOn.addProject(projectStimulsoftBase);
            projectStimulsoftDesignerFx.DependsOn.addProject(projectStimulsoftDatabase);
            projectStimulsoftDesignerFx.DependsOn.addProject(projectStimulsoftReport);
            projectStimulsoftDesignerFx.DependsOn.addProject(projectStimulsoftPreloader);
            projectStimulsoftDesignerFx.DependsOn.addProject(projectStimulsoftControlsFx);
            projectStimulsoftDesignerFx.DependsOn.addProject(projectApiProviderFlex);
            projectStimulsoftDesignerFx.DependsOn.addProject(projectStimulsoftPropertyGrid);
            projectStimulsoftDesignerFx.DependsOn.addProject(projectStimulsoftViewerFx, false);
            projectStimulsoftDesignerFx.DependsOn.addProject(projectStimulsoftDesignerFxImages);
            projectStimulsoftDesignerFx.DependsOn.addProject(projectStimulsoftDesignerFxImages2013);
            projectStimulsoftDesignerFx.DependsOn.addProject(projectStimulsoftReportCheck);
            projects.addProject(projectStimulsoftDesignerFx);

            #endregion
            
            #region PHP

            Project projectDesignerFxPhp = new Project("DesignerFx_PHP", ProjectType.Swf, Path.Combine(ProjectHelper.FlexSources, "DesignerFx_PHP\\src"), "DesignerFx_PHP.swf");
            projectDesignerFxPhp.DependsOn.addProject(projectStimulsoftBase);
            projectDesignerFxPhp.DependsOn.addProject(projectStimulsoftDatabase);
            projectDesignerFxPhp.DependsOn.addProject(projectStimulsoftReport);
            projectDesignerFxPhp.DependsOn.addProject(projectStimulsoftPreloader);
            projectDesignerFxPhp.DependsOn.addProject(projectStimulsoftControlsFx);
            projectDesignerFxPhp.DependsOn.addProject(projectApiProviderPhp);
            projectDesignerFxPhp.DependsOn.addProject(projectStimulsoftPropertyGrid);
            projectDesignerFxPhp.DependsOn.addProject(projectStimulsoftViewerFx);
            projectDesignerFxPhp.DependsOn.addProject(projectStimulsoftReportCheck);
            projectDesignerFxPhp.DependsOn.addProject(projectStimulsoftDesignerFx);
            projectDesignerFxPhp.DependsOn.addProject(projectStimulsoftDesignerFxImages);
            projectDesignerFxPhp.DependsOn.addProject(projectStimulsoftDesignerFxImages2013);
            projectDesignerFxPhp.MainClass = "DesignerFx_PHP.mxml";
            projectDesignerFxPhp.ApplicationType = ApplicationType.PHP;
            projects.addProject(projectDesignerFxPhp);

            Project projectViewerFxPhp = new Project("ViewerFx_PHP", ProjectType.Swf, Path.Combine(ProjectHelper.FlexSources, "ViewerFx_PHP\\src"), "ViewerFx_PHP.swf");
            projectViewerFxPhp.DependsOn.addProject(projectStimulsoftBase);
            projectViewerFxPhp.DependsOn.addProject(projectStimulsoftDatabase);
            projectViewerFxPhp.DependsOn.addProject(projectStimulsoftReport);
            projectViewerFxPhp.DependsOn.addProject(projectStimulsoftPreloader);
            projectViewerFxPhp.DependsOn.addProject(projectStimulsoftControlsFx);
            projectViewerFxPhp.DependsOn.addProject(projectApiProviderPhp);
            projectViewerFxPhp.DependsOn.addProject(projectStimulsoftPropertyGrid);
            projectViewerFxPhp.DependsOn.addProject(projectStimulsoftViewerFx);
            projectViewerFxPhp.MainClass = "ViewerFx_PHP.mxml";
            projectViewerFxPhp.ApplicationType = ApplicationType.PHP;
            projects.addProject(projectViewerFxPhp);

            #endregion

            #region Java

            Project projectDesignerFxJava = new Project("DesignerFx_Java", ProjectType.Swf, Path.Combine(ProjectHelper.FlexSources, "DesignerFx_Java\\src"), "DesignerFx.swf");
            projectDesignerFxJava.DependsOn.addProject(projectStimulsoftBase);
            projectDesignerFxJava.DependsOn.addProject(projectStimulsoftDatabase);
            projectDesignerFxJava.DependsOn.addProject(projectStimulsoftReport);
            projectDesignerFxJava.DependsOn.addProject(projectStimulsoftPreloader);
            projectDesignerFxJava.DependsOn.addProject(projectStimulsoftControlsFx);
            projectDesignerFxJava.DependsOn.addProject(projectApiProviderJava);
            projectDesignerFxJava.DependsOn.addProject(projectStimulsoftPropertyGrid);
            projectDesignerFxJava.DependsOn.addProject(projectStimulsoftViewerFx);
            projectDesignerFxJava.DependsOn.addProject(projectStimulsoftReportCheck);
            projectDesignerFxJava.DependsOn.addProject(projectStimulsoftDesignerFx);
            projectDesignerFxJava.DependsOn.addProject(projectStimulsoftDesignerFxImages);
            projectDesignerFxJava.DependsOn.addProject(projectStimulsoftDesignerFxImages2013);
            projectDesignerFxJava.MainClass = "DesignerFx_Java.mxml";
            projectDesignerFxJava.ApplicationType = ApplicationType.Java;
            projects.addProject(projectDesignerFxJava);

            Project projectViewerFxJava = new Project("ViewerFx_Java", ProjectType.Swf, Path.Combine(ProjectHelper.FlexSources, "ViewerFx_Java\\src"), "ViewerFx.swf");
            projectViewerFxJava.DependsOn.addProject(projectStimulsoftBase);
            projectViewerFxJava.DependsOn.addProject(projectStimulsoftDatabase);
            projectViewerFxJava.DependsOn.addProject(projectStimulsoftReport);
            projectViewerFxJava.DependsOn.addProject(projectStimulsoftPreloader);
            projectViewerFxJava.DependsOn.addProject(projectStimulsoftControlsFx);
            projectViewerFxJava.DependsOn.addProject(projectApiProviderJava);
            projectViewerFxJava.DependsOn.addProject(projectStimulsoftPropertyGrid);
            projectViewerFxJava.DependsOn.addProject(projectStimulsoftViewerFx);
            projectViewerFxJava.MainClass = "ViewerFx_Java.mxml";
            projectViewerFxJava.ApplicationType = ApplicationType.Java;
            projects.addProject(projectViewerFxJava);
            
            #endregion

            #region Web

            Project projectDesignerFxWeb = new Project("DesignerFx_Web", ProjectType.Swf, Path.Combine(ProjectHelper.FlexSources, "DesignerFx_Web\\src"), "DesignerFx.swf");
            projectDesignerFxWeb.DependsOn.addProject(projectStimulsoftBase);
            projectDesignerFxWeb.DependsOn.addProject(projectStimulsoftDatabase);
            projectDesignerFxWeb.DependsOn.addProject(projectStimulsoftReport);
            projectDesignerFxWeb.DependsOn.addProject(projectStimulsoftPreloader);
            projectDesignerFxWeb.DependsOn.addProject(projectStimulsoftControlsFx);
            projectDesignerFxWeb.DependsOn.addProject(projectApiProviderWeb);
            projectDesignerFxWeb.DependsOn.addProject(projectStimulsoftPropertyGrid);
            projectDesignerFxWeb.DependsOn.addProject(projectStimulsoftViewerFx);
            projectDesignerFxWeb.DependsOn.addProject(projectStimulsoftReportCheck);
            projectDesignerFxWeb.DependsOn.addProject(projectStimulsoftDesignerFx);
            projectDesignerFxWeb.DependsOn.addProject(projectStimulsoftDesignerFxImages);
            projectDesignerFxWeb.DependsOn.addProject(projectStimulsoftDesignerFxImages2013);
            projectDesignerFxWeb.MainClass = "DesignerFx_Web.mxml";
            projectDesignerFxWeb.ApplicationType = ApplicationType.Web;
            projects.addProject(projectDesignerFxWeb);

            Project projectViewerFxWeb = new Project("ViewerFx_Web", ProjectType.Swf, Path.Combine(ProjectHelper.FlexSources, "ViewerFx_Web\\src"), "ViewerFx.swf");
            projectViewerFxWeb.DependsOn.addProject(projectStimulsoftBase);
            projectViewerFxWeb.DependsOn.addProject(projectStimulsoftDatabase);
            projectViewerFxWeb.DependsOn.addProject(projectStimulsoftReport);
            projectViewerFxWeb.DependsOn.addProject(projectStimulsoftPreloader);
            projectViewerFxWeb.DependsOn.addProject(projectStimulsoftControlsFx);
            projectViewerFxWeb.DependsOn.addProject(projectApiProviderWeb);
            projectViewerFxWeb.DependsOn.addProject(projectStimulsoftPropertyGrid);
            projectViewerFxWeb.DependsOn.addProject(projectStimulsoftViewerFx);
            projectViewerFxWeb.MainClass = "ViewerFx_Web.mxml";
            projectViewerFxWeb.ApplicationType = ApplicationType.Web;
            projects.addProject(projectViewerFxWeb);

            Project projectViewerFxJavaScript = new Project("ViewerFx_JavaScript", ProjectType.Swf, Path.Combine(ProjectHelper.FlexSources, "ViewerFx_JavaScript\\src"), "ViewerFx_JavaScript.swf");
            projectViewerFxJavaScript.DependsOn.addProject(projectStimulsoftBase);
            projectViewerFxJavaScript.DependsOn.addProject(projectStimulsoftDatabase);
            projectViewerFxJavaScript.DependsOn.addProject(projectStimulsoftReport);
            projectViewerFxJavaScript.DependsOn.addProject(projectStimulsoftPreloader);
            projectViewerFxJavaScript.DependsOn.addProject(projectStimulsoftControlsFx);
            projectViewerFxJavaScript.DependsOn.addProject(projectStimulsoftPropertyGrid);
            projectViewerFxJavaScript.DependsOn.addProject(projectStimulsoftViewerFx);
            projectViewerFxJavaScript.MainClass = "ViewerFx_JavaScript.mxml";
            projectViewerFxJavaScript.ApplicationType = ApplicationType.Web;
            projects.addProject(projectViewerFxJavaScript);

            #endregion

            #region DesignerFx App

            Project projectDesignerFlexAir = new Project("DesignerFx", ProjectType.Air, Path.Combine(ProjectHelper.FlexSources, "DesignerFx_AIR\\src"), "DesignerFx.swf");
            projectDesignerFlexAir.DependsOn.addProject(projectStimulsoftBase);
            projectDesignerFlexAir.DependsOn.addProject(projectStimulsoftDatabase);
            projectDesignerFlexAir.DependsOn.addProject(projectStimulsoftReport);
            projectDesignerFlexAir.DependsOn.addProject(projectStimulsoftPreloader);
            projectDesignerFlexAir.DependsOn.addProject(projectStimulsoftControlsFx);
            projectDesignerFlexAir.DependsOn.addProject(projectApiProviderAir);
            projectDesignerFlexAir.DependsOn.addProject(projectStimulsoftPropertyGrid);
            projectDesignerFlexAir.DependsOn.addProject(projectStimulsoftViewerFx);
            projectDesignerFlexAir.DependsOn.addProject(projectStimulsoftReportCheck);
            projectDesignerFlexAir.DependsOn.addProject(projectStimulsoftDesignerFxImages);
            projectDesignerFlexAir.DependsOn.addProject(projectStimulsoftDesignerFxImages2013);
            projectDesignerFlexAir.DependsOn.addProject(projectStimulsoftDesignerFx);
            projectDesignerFlexAir.Name = "DesignerFx";
            projectDesignerFlexAir.MainClass = "DesignerFx_AIR.mxml";
            projectDesignerFlexAir.Packed.Add("config.xml");
            projectDesignerFlexAir.Packed.Add("icons");
            projects.addProject(projectDesignerFlexAir);

            #endregion

            #region DemoFx App

            Project projectDemoFlexAir = new Project("DemoFx", ProjectType.Air, Path.Combine(ProjectHelper.FlexSources, "DemoFx_Air\\src"), "DemoFx.swf");
            projectDemoFlexAir.DependsOn.addProject(projectStimulsoftBase);
            projectDemoFlexAir.DependsOn.addProject(projectStimulsoftDatabase);
            projectDemoFlexAir.DependsOn.addProject(projectStimulsoftReport);
            projectDemoFlexAir.DependsOn.addProject(projectStimulsoftPreloader);
            projectDemoFlexAir.DependsOn.addProject(projectStimulsoftControlsFx);
            projectDemoFlexAir.DependsOn.addProject(projectApiProviderAir);
            projectDemoFlexAir.DependsOn.addProject(projectStimulsoftPropertyGrid);
            projectDemoFlexAir.DependsOn.addProject(projectStimulsoftViewerFx);
            projectDemoFlexAir.DependsOn.addProject(projectStimulsoftReportCheck);
            projectDemoFlexAir.DependsOn.addProject(projectStimulsoftDesignerFx);
            projectDemoFlexAir.DependsOn.addProject(projectStimulsoftDesignerFxImages);
            projectDemoFlexAir.DependsOn.addProject(projectStimulsoftDesignerFxImages2013);
            projectDemoFlexAir.Name = "DemoFx";
            projectDemoFlexAir.MainClass = "DemoFx_Air.mxml";
            projectDemoFlexAir.Packed.Add("icons");
            projectDemoFlexAir.Packed.Add("reports");
            projectDemoFlexAir.Packed.Add("data");
            projects.addProject(projectDemoFlexAir);

            #endregion

            #region Demo_Flex

            Project projectDemoFlexSwf = new Project("Demo_Flex", ProjectType.Swf, Path.Combine(ProjectHelper.FlexSources, "DemoFx_Flex\\src"), "Demo_Flex.swf");
            projectDemoFlexSwf.DependsOn.addProject(projectStimulsoftBase);
            projectDemoFlexSwf.DependsOn.addProject(projectStimulsoftDatabase);
            projectDemoFlexSwf.DependsOn.addProject(projectStimulsoftReport);
            projectDemoFlexSwf.DependsOn.addProject(projectStimulsoftPreloader);
            projectDemoFlexSwf.DependsOn.addProject(projectStimulsoftControlsFx);
            projectDemoFlexSwf.DependsOn.addProject(projectApiProviderFlex);
            projectDemoFlexSwf.DependsOn.addProject(projectStimulsoftPropertyGrid);
            projectDemoFlexSwf.DependsOn.addProject(projectStimulsoftViewerFx);
            projectDemoFlexSwf.DependsOn.addProject(projectStimulsoftReportCheck);
            projectDemoFlexSwf.DependsOn.addProject(projectStimulsoftDesignerFx);
            projectDemoFlexSwf.DependsOn.addProject(projectStimulsoftDesignerFxImages);
            projectDemoFlexSwf.DependsOn.addProject(projectStimulsoftDesignerFxImages2013);
            projectDemoFlexSwf.Name = "Demo_Flex";
            projectDemoFlexSwf.MainClass = "DemoFx_Flex.mxml";
            projects.addProject(projectDemoFlexSwf);

            #endregion

            #region ExportsFx

            Project projectExportsFxAir = new Project("ExportsFx", ProjectType.Air, Path.Combine(ProjectHelper.FlexSources, "ExportsFx\\src"), "ExportsFx.swf");
            projectExportsFxAir.DependsOn.addProject(projectStimulsoftBase);
            projectExportsFxAir.DependsOn.addProject(projectStimulsoftDatabase);
            projectExportsFxAir.DependsOn.addProject(projectStimulsoftReport);
            projectExportsFxAir.DependsOn.addProject(projectStimulsoftPreloader);
            projectExportsFxAir.DependsOn.addProject(projectStimulsoftControlsFx);
            projectExportsFxAir.DependsOn.addProject(projectApiProviderAir);
            projectExportsFxAir.DependsOn.addProject(projectStimulsoftDesignerFxImages);
            projectExportsFxAir.DependsOn.addProject(projectStimulsoftDesignerFxImages2013);
            projectExportsFxAir.Name = "ExportsFx";
            projectExportsFxAir.MainClass = "ExportsFx.mxml";
            projects.addProject(projectExportsFxAir);

            #endregion
        }

        private void analyzeProjects()
        {
            projects.clearSelected();

            projects["Stimulsoft_DesignerFx"].Selected = checkBoxFlex.Checked;
            projects["Stimulsoft_ViewerFx"].Selected = checkBoxFlex.Checked;

            projects["DesignerFx_PHP"].Selected = checkBoxPhp.Checked;
            projects["ViewerFx_PHP"].Selected = checkBoxPhp.Checked;

            projects["DesignerFx_Java"].Selected = checkBoxJava.Checked || checkBoxJavaOnlySwf.Checked;
            projects["ViewerFx_Java"].Selected = checkBoxJava.Checked || checkBoxJavaOnlySwf.Checked;

            projects["DesignerFx_Web"].Selected = checkBoxWeb.Checked;
            projects["ViewerFx_Web"].Selected = checkBoxWeb.Checked;
            projects["ViewerFx_JavaScript"].Selected = checkBoxWeb.Checked;

            projects["DesignerFx"].Selected = checkBoxApps.Checked || checkBoxFlex.Checked || checkBoxPhp.Checked || checkBoxJava.Checked;
            projects["ExportsFx"].Selected = checkBoxApps.Checked || checkBoxFlex.Checked || checkBoxPhp.Checked || checkBoxJava.Checked;
            projects["DemoFx"].Selected = checkBoxApps.Checked;
            projects["Demo_Flex"].Selected = checkBoxFlex.Checked;
        }

        private void buildProjects()
        {
            saveConfig();
            
            DateTime timeStart = DateTime.Now;
            ProjectHelper.Dialog = new DialogForm();
            ProjectHelper.Dialog.Show(this);

            if (tempDirectory.Exists) tempDirectory.Delete(true);

            ProjectHelper.Dialog.Clear();
            
            #region Compile Flex

            if (checkBoxFlex.Checked || checkBoxPhp.Checked || checkBoxApps.Checked)
            {
                ProjectHelper.Dialog.AppendLine("Compile Flex");
                ProjectHelper.Dialog.AppendLine("---------------------------------------------------------------");

                projects.clearCompiling();
                foreach (Project project in projects.getSortedProject())
                {
                    if (!ProjectHelper.Dialog.Visible) return;

                    if (project.ApplicationType != ApplicationType.Java && 
                        project.ApplicationType != ApplicationType.Web &&
                        !ProjectHelper.compile(project, true, true, false, false, false, false)) return;

                    if (project.Type == ProjectType.Air && !ProjectHelper.compile(project, false, true, false, false, true, false)) return;
                }
                ProjectHelper.Dialog.AppendLine("");
            }

            #endregion

            #region Compile Flex with Debug

            if (checkBoxFlex.Checked)
            {
                ProjectHelper.Dialog.AppendLine("Compile Flex with Debug");
                ProjectHelper.Dialog.AppendLine("---------------------------------------------------------------");

                projects.clearCompiling();
                foreach (Project project in projects.getSortedProject())
                {
                    if (!ProjectHelper.Dialog.Visible) return;

                    if (project.Type == ProjectType.Swc &&
                        project.ApplicationType != ApplicationType.PHP &&
                        project.ApplicationType != ApplicationType.Java &&
                        project.ApplicationType != ApplicationType.Web &&
                        !ProjectHelper.compile(project, false, true, true, false, false, false)) return;
                }
                ProjectHelper.Dialog.AppendLine("");
            }

            #endregion
            
            #region Compile Web

            if (checkBoxWeb.Checked || checkBoxOnlyWeb.Checked)
            {
                ProjectHelper.Dialog.AppendLine("Compile Web");
                ProjectHelper.Dialog.AppendLine("---------------------------------------------------------------");

                projects.clearCompiling();
                foreach (Project project in projects.getSortedProject())
                {
                    if (!ProjectHelper.Dialog.Visible) return;

                    if (project.ApplicationType == ApplicationType.Web ||
                        project.ApplicationType == ApplicationType.Flex && project.Type == ProjectType.Swc)
                    {
                        bool copySrc = project.ApplicationType == ApplicationType.Web || !(checkBoxFlex.Checked || checkBoxPhp.Checked);
                        if (!ProjectHelper.compile(project, copySrc, false, false, true, false, false)) return;
                    }
                }
                ProjectHelper.Dialog.AppendLine("");
            }

            #endregion

            #region Compile Java

            if (checkBoxJava.Checked || checkBoxJavaOnlySwf.Checked)
            {
                ProjectHelper.Dialog.AppendLine("Compile Java");
                ProjectHelper.Dialog.AppendLine("---------------------------------------------------------------");

                projects.clearCompiling();
                foreach (Project project in projects.getSortedProject())
                {
                    if (!ProjectHelper.Dialog.Visible) return;

                    if (project.ApplicationType == ApplicationType.Java ||
                        project.ApplicationType == ApplicationType.Flex && project.Type == ProjectType.Swc)
                    {
                        bool copySrc = project.ApplicationType == ApplicationType.Java || !(checkBoxFlex.Checked || checkBoxPhp.Checked);
                        if (!ProjectHelper.compile(project, copySrc, false, false, false, false, true)) return;
                    }
                }
                ProjectHelper.Dialog.AppendLine("");
            }

            #endregion

            ProjectHelper.Dialog.AppendLine("---------------------------------------------------------------");
            ProjectHelper.Dialog.AppendLine("End compiling " + new DateTime((DateTime.Now - timeStart).Ticks).ToString("mm:ss"));

            makeProjects();

            ProjectHelper.Dialog.AppendLine("");
            ProjectHelper.Dialog.AppendLine("");
            ProjectHelper.Dialog.AppendLine("");
            ProjectHelper.Dialog.AppendLine("=== Complete " + new DateTime((DateTime.Now - timeStart).Ticks).ToString("mm:ss") + " ===");

            ProjectHelper.Dialog.flashForm();
        }
        
        private void makeProjects()
        {
            if (makeDirectory.Exists) makeDirectory.Delete(true);
            makeDirectory.Create();

            string dateTime = ProjectHelper.DateBuild.ToString("yyyy.MM.dd");
            string pathFlex = string.Format("{0}\\FLEX_{1}\\", makeDirectory.FullName, dateTime);
            string pathPHP = string.Format("{0}\\PHP_{1}\\", makeDirectory.FullName, dateTime);
            string pathJava = string.Format("{0}\\JAVA_{1}\\", makeDirectory.FullName, dateTime);
            string pathJS = string.Format("{0}\\JS_{1}\\", makeDirectory.FullName, dateTime);
            string pathDbsJS = string.Format("{0}\\DBS_JS_{1}\\", makeDirectory.FullName, dateTime);
            string pathSWF = string.Format("{0}\\SWF", makeDirectory.FullName);
            string pathApps = string.Format("{0}\\Apps", makeDirectory.FullName);
            string pathSources = string.Format("{0}\\Sources_{1}", makeDirectory.FullName, dateTime);
            if (checkBoxFlex.Checked || checkBoxJava.Checked || checkBoxJavaOnlySwf.Checked || checkBoxWeb.Checked) Directory.CreateDirectory(pathSWF);
            if (checkBoxSources.Checked && (checkBoxFlex.Checked || checkBoxPhp.Checked || checkBoxJava.Checked || checkBoxWeb.Checked)) Directory.CreateDirectory(pathSources);

            #region Copy compiled files
            
            DateTime timeStart = DateTime.Now;

            ProjectHelper.Dialog.AppendLine("");
            ProjectHelper.Dialog.AppendLine("");
            ProjectHelper.Dialog.AppendLine("Copy compiled files");
            ProjectHelper.Dialog.AppendLine("---------------------------------------------------------------");

            string fileDest = "";

            #region Flex Components
            
            if (checkBoxFlex.Checked)
            {
                ProjectHelper.Dialog.Append("Reports.Flex..... ");

                Directory.CreateDirectory(pathFlex + "Apps");
                Directory.CreateDirectory(pathFlex + "Components");

                foreach (FileInfo file in tempDirectory.GetFiles("*.*", SearchOption.TopDirectoryOnly))
                {
                    switch (file.Name)
                    {
                        case "Stimulsoft_ViewerFx.swc":
                        case "Stimulsoft_ViewerFx_Debug.swc":
                        case "Stimulsoft_DesignerFx.swc":
                        case "Stimulsoft_DesignerFx_Debug.swc":
                            fileDest = Path.Combine(pathFlex + "Components", file.Name);
                            break;

                        case "DesignerFx.air":
                        case "ExportsFx.air":
                            fileDest = Path.Combine(pathFlex + "Apps", file.Name);
                            break;

                        case "Demo_Flex.swf":
                            fileDest = Path.Combine(pathSWF, file.Name);
                            break;
                    }

                    if (!string.IsNullOrEmpty(fileDest)) file.CopyTo(fileDest);
                    fileDest = "";
                }

                if (checkBoxSources.Checked) ProjectHelper.copyFlexSources(pathSources, "Flex");

                ProjectHelper.Dialog.AppendLine("OK");
            }
            
            #endregion

            #region PHP Components
            
            if (checkBoxPhp.Checked)
            {
                ProjectHelper.Dialog.Append("Reports.PHP..... ");

                ProjectHelper.copyDirectory("files\\php\\Flex", pathPHP + "Flex");
                ProjectHelper.replaceVersion(pathPHP + "Flex\\stimulsoft\\viewer.html");
                ProjectHelper.replaceVersion(pathPHP + "Flex\\stimulsoft\\designer.html");
                ProjectHelper.copyDirectory("files\\php\\JS", pathPHP + "JS");
                ProjectHelper.copyDirectory("files\\js\\dbs\\components\\Css", pathPHP + "JS\\css");
                ProjectHelper.copyDirectory("files\\js\\dbs\\components\\Scripts", pathPHP + "JS\\scripts");
                ProjectHelper.copyDirectory("files\\js\\dbs\\components\\Designers", pathPHP + "Designers");
                ProjectHelper.copyDirectory("files\\flex\\DesignerFx", pathPHP + "Designers\\Flex-Windows");
                Directory.CreateDirectory(pathPHP + "Designers\\Flex-AIR");
                Directory.CreateDirectory(pathPHP + "Apps");

                foreach (FileInfo file in tempDirectory.GetFiles("*.*", SearchOption.TopDirectoryOnly))
                {
                    switch (file.Name)
                    {
                        case "ViewerFx_PHP.swf":
                        case "DesignerFx_PHP.swf":
                            fileDest = Path.Combine(pathPHP + "Flex\\stimulsoft", file.Name);
                            break;

                        case "DesignerFx.air":
                            fileDest = Path.Combine(pathPHP + "Designers\\Flex-AIR", file.Name);
                            break;

                        case "DesignerFx.swf":
                            fileDest = Path.Combine(pathPHP + "Designers\\Flex-Windows", file.Name);
                            break;
                            
                        case "ExportsFx.air":
                            fileDest = Path.Combine(pathPHP + "Apps", file.Name);
                            break;
                    }

                    if (!string.IsNullOrEmpty(fileDest)) file.CopyTo(fileDest);
                    fileDest = "";
                }

                if (checkBoxSources.Checked)
                {
                    ProjectHelper.copyFlexSources(pathSources, "PHP-Flex");
                    ProjectHelper.copyDirectory("files\\js\\reports\\src", Path.Combine(pathSources, "PHP-JS"));
                }

                ProjectHelper.Dialog.AppendLine("OK");
            }
            
            #endregion

            #region Java Components
            
            if (checkBoxJava.Checked && !checkBoxJavaOnlySwf.Checked)
            {
                ProjectHelper.Dialog.Append("Reports.Java..... ");

                ProjectHelper.copyDirectory("files\\java\\components", pathJava + "Components");
                ProjectHelper.copyDirectory("files\\java\\external", pathJava + "Components\\External");
                ProjectHelper.copyDirectory("files\\js\\reports\\components\\Designers", pathJava + "Designers");
                ProjectHelper.copyDirectory("files\\flex\\DesignerFx", pathJava + "Designers\\Flex-Windows");
                Directory.CreateDirectory(pathJava + "Designers\\Flex-AIR");

                foreach (FileInfo file in tempDirectory.GetFiles("*.*", SearchOption.TopDirectoryOnly))
                {
                    switch (file.Name)
                    {
                        case "ViewerFx_Java.swf":
                        case "DesignerFx_Java.swf":
                            fileDest = Path.Combine(pathSWF, file.Name);
                            break;

                        case "DesignerFx.air":
                            fileDest = Path.Combine(pathJava + "Designers\\Flex-AIR", file.Name);
                            break;

                        case "DesignerFx.swf":
                            fileDest = Path.Combine(pathJava + "Designers\\Flex-Windows", file.Name);
                            break;
                    }

                    if (!string.IsNullOrEmpty(fileDest)) file.CopyTo(fileDest);
                    fileDest = "";
                }

                if (checkBoxSources.Checked)
                {
                    ProjectHelper.copyFlexSources(pathSources, "Java-Flex");
                    ProjectHelper.copyDirectory("files\\java\\sources", Path.Combine(pathSources, "Java"));
                }

                ProjectHelper.Dialog.AppendLine("OK");
            }
            
            #endregion

            #region JS Components
            
            if (checkBoxRepackJs.Checked)
            {
                ProjectHelper.Dialog.Append("Reports.JS..... ");
                ProjectHelper.copyDirectory("files\\js\\reports\\components", pathJS);
                if (checkBoxSources.Checked) ProjectHelper.copyDirectory("files\\js\\reports\\src", Path.Combine(pathSources, "JS"));
                ProjectHelper.Dialog.AppendLine("OK");

                ProjectHelper.Dialog.Append("Dashboards.JS..... ");
                ProjectHelper.copyDirectory("files\\js\\dbs\\components", pathDbsJS);
                if (checkBoxSources.Checked) ProjectHelper.copyDirectory("files\\js\\dbs\\src", Path.Combine(pathSources, "DBS-JS"));
                ProjectHelper.Dialog.AppendLine("OK");
            }
            
            #endregion

            #region Java SWF Files
            
            if (checkBoxJavaOnlySwf.Checked)
            {
                ProjectHelper.Dialog.Append("Java Components..... ");

                File.Copy(Path.Combine(tempDirectory.FullName, "ViewerFx_Java.swf"), Path.Combine(pathSWF, "ViewerFx_Java.swf"));
                File.Copy(Path.Combine(tempDirectory.FullName, "DesignerFx_Java.swf"), Path.Combine(pathSWF, "DesignerFx_Java.swf"));

                ProjectHelper.Dialog.AppendLine("OK");
            }

            #endregion

            #region Web Components

            if (checkBoxWeb.Checked || checkBoxOnlyWeb.Checked)
            {
                ProjectHelper.Dialog.Append("Web Components..... ");

                File.Copy(Path.Combine(tempDirectory.FullName, "ViewerFx_Web.swf"), Path.Combine(pathSWF, "ViewerFx_Web.swf"));
                File.Copy(Path.Combine(tempDirectory.FullName, "DesignerFx_Web.swf"), Path.Combine(pathSWF, "DesignerFx_Web.swf"));
                File.Copy(Path.Combine(tempDirectory.FullName, "ViewerFx_JavaScript.swf"), Path.Combine(pathSWF, "ViewerFx_JavaScript.swf"));

                if (checkBoxSources.Checked) ProjectHelper.copyFlexSources(pathSources, "Web-Flex");

                ProjectHelper.Dialog.AppendLine("OK");
            }
            
            #endregion

            #region AIR Apps
            
            if (checkBoxApps.Checked)
            {
                ProjectHelper.Dialog.Append("AIR Apps..... ");

                Directory.CreateDirectory(pathApps);
                ProjectHelper.copyDirectory("files\\flex\\DesignerFx", pathApps + "\\DesignerFx");
                ProjectHelper.copyDirectory("files\\flex\\ExportsFx", pathApps + "\\ExportsFx");
                ProjectHelper.copyDirectory("files\\flex\\DemoFx", pathApps + "\\DemoFx");

                File.Copy(Path.Combine(tempDirectory.FullName, "DesignerFx.air"), Path.Combine(pathApps, "DesignerFx.air"));
                File.Copy(Path.Combine(tempDirectory.FullName, "DesignerFx.swf"), Path.Combine(pathApps, "DesignerFx\\DesignerFx.swf"));
                File.Copy(Path.Combine(tempDirectory.FullName, "ExportsFx.air"), Path.Combine(pathApps, "ExportsFx.air"));
                File.Copy(Path.Combine(tempDirectory.FullName, "ExportsFx.swf"), Path.Combine(pathApps, "ExportsFx\\ExportsFx.swf"));
                File.Copy(Path.Combine(tempDirectory.FullName, "DemoFx.air"), Path.Combine(pathApps, "DemoFx.air"));
                File.Copy(Path.Combine(tempDirectory.FullName, "DemoFx.swf"), Path.Combine(pathApps, "DemoFx\\DemoFx.swf"));

                ProjectHelper.Dialog.AppendLine("OK");
            }

            #endregion

            ProjectHelper.Dialog.AppendLine("");
            ProjectHelper.Dialog.AppendLine("---------------------------------------------------------------");
            ProjectHelper.Dialog.AppendLine("End copying " + new DateTime((DateTime.Now - timeStart).Ticks).ToString("mm:ss"));

            #endregion

            #region Create Zip archives

            if (checkBoxZip.Checked)
            {
                timeStart = DateTime.Now;

                ProjectHelper.Dialog.AppendLine("");
                ProjectHelper.Dialog.AppendLine("");
                ProjectHelper.Dialog.AppendLine("Create Zip archives");
                ProjectHelper.Dialog.AppendLine("---------------------------------------------------------------");

                if (checkBoxApps.Checked && ProjectHelper.Dialog.Visible) ProjectHelper.archive(pathApps, "Apps", false);
                if (checkBoxSources.Checked && ProjectHelper.Dialog.Visible) ProjectHelper.archive(pathSources, "Sources", false);
                if (checkBoxFlex.Checked && ProjectHelper.Dialog.Visible) ProjectHelper.archive(pathFlex, "Reports.Flex", true);
                if (checkBoxPhp.Checked && ProjectHelper.Dialog.Visible) ProjectHelper.archive(pathPHP, "Reports.PHP", true);
                if (checkBoxJava.Checked && ProjectHelper.Dialog.Visible) ProjectHelper.archive(pathJava, "Reports.Java", true);
                if (checkBoxRepackJs.Checked && ProjectHelper.Dialog.Visible)
                {
                    ProjectHelper.archive(pathJS, "Reports.JS", true);
                    ProjectHelper.archive(pathDbsJS, "Dashboards.JS", true);
                }
                if (checkBoxBuildZip.Checked && ProjectHelper.Dialog.Visible)
                {
                    string buildFileName = string.Format("Build_{0}_{1}", ProjectHelper.Version, dateTime);
                    string pathBuild = Path.Combine(makeDirectory.FullName, buildFileName);
                    Directory.CreateDirectory(pathBuild);
                    foreach (FileInfo file in makeDirectory.GetFiles("*.*", SearchOption.TopDirectoryOnly))
                    {
                        file.MoveTo(Path.Combine(pathBuild, file.Name));
                    }

                    if (ProjectHelper.Dialog.Visible) ProjectHelper.archive(pathBuild, "Build", false);
                }

                ProjectHelper.Dialog.AppendLine("");
                ProjectHelper.Dialog.AppendLine("---------------------------------------------------------------");
                ProjectHelper.Dialog.AppendLine("End archiving " + new DateTime((DateTime.Now - timeStart).Ticks).ToString("mm:ss"));
            }
            
            #endregion
        }

        private void loadConfig()
        {
            XmlDocument config = new XmlDocument();
            config.Load("config.xml");

            foreach (XmlNode node in config.ChildNodes)
            {
                if (node.Name == "Version") textBoxVersion.Text = node.InnerText;
            }
        }

        private void saveConfig()
        {
            ProjectHelper.Version = textBoxVersion.Text;
            XmlDocument config = new XmlDocument();
            config.Load("config.xml");

            foreach (XmlNode node in config.ChildNodes)
            {
                if (node.Name == "Version") node.InnerText = textBoxVersion.Text;
            }

            config.Save("config.xml");
        }

        public Builder()
        {
            InitializeComponent();
            loadConfig();
            dateTimePicker.Value = DateTime.Now;
        }

        private void buttonBuild_Click(object sender, EventArgs e)
        {
            createSheme();
            analyzeProjects();
            buildProjects();
        }

        private void dateTimePicker1_ValueChanged(object sender, EventArgs e)
        {
            ProjectHelper.DateBuild = dateTimePicker.Value;
        }

        private void Form1_FormClosing(object sender, FormClosingEventArgs e)
        {
            Thread.Sleep(1000);
            DirectoryInfo temp = new DirectoryInfo(ProjectHelper.TempPath);
            if (temp.Exists) temp.Delete(true);
        }

        private void checkBoxJavaOnlySwf_CheckedChanged(object sender, EventArgs e)
        {
            if (checkBoxJavaOnlySwf.Checked || checkBoxOnlyWeb.Checked)
            {
                checkBoxesState["checkBoxFlex"] = checkBoxFlex.Checked;
                checkBoxesState["checkBoxPhp"] = checkBoxPhp.Checked;
                checkBoxesState["checkBoxJava"] = checkBoxJava.Checked;
                checkBoxesState["checkBoxWeb"] = checkBoxWeb.Checked;
                checkBoxesState["checkBoxApps"] = checkBoxApps.Checked;
                checkBoxesState["checkBoxRepackJs"] = checkBoxRepackJs.Checked;
                checkBoxesState["checkBoxSources"] = checkBoxSources.Checked;
                checkBoxesState["checkBoxZip"] = checkBoxZip.Checked;

                checkBoxFlex.Checked = false;
                checkBoxPhp.Checked = false;
                checkBoxJava.CheckState = checkBoxJavaOnlySwf.Checked ? CheckState.Indeterminate : CheckState.Unchecked;
                checkBoxWeb.Checked = checkBoxOnlyWeb.Checked;
                checkBoxApps.Checked = false;
                checkBoxRepackJs.Checked = false;
                checkBoxSources.Checked = false;
                checkBoxZip.Checked = false;
            }
            else
            {
                checkBoxFlex.Checked = (bool)checkBoxesState["checkBoxFlex"];
                checkBoxPhp.Checked = (bool)checkBoxesState["checkBoxPhp"];
                checkBoxJava.CheckState = CheckState.Unchecked;
                checkBoxJava.Checked = (bool)checkBoxesState["checkBoxJava"];
                checkBoxWeb.Checked = (bool)checkBoxesState["checkBoxWeb"];
                checkBoxApps.Checked = (bool)checkBoxesState["checkBoxApps"];
                checkBoxRepackJs.Checked = (bool)checkBoxesState["checkBoxRepackJs"];
                checkBoxSources.Checked = (bool)checkBoxesState["checkBoxSources"];
                checkBoxZip.Checked = (bool)checkBoxesState["checkBoxZip"];
            }

            checkBoxFlex.Enabled = !checkBoxJavaOnlySwf.Checked && !checkBoxOnlyWeb.Checked;
            checkBoxPhp.Enabled = !checkBoxJavaOnlySwf.Checked && !checkBoxOnlyWeb.Checked;
            checkBoxJava.Enabled = !checkBoxJavaOnlySwf.Checked && !checkBoxOnlyWeb.Checked;
            checkBoxJavaOnlySwf.Enabled = !checkBoxOnlyWeb.Checked;
            checkBoxWeb.Enabled = !checkBoxJavaOnlySwf.Checked && !checkBoxOnlyWeb.Checked;
            checkBoxOnlyWeb.Enabled = !checkBoxJavaOnlySwf.Checked;
            checkBoxApps.Enabled = !checkBoxJavaOnlySwf.Checked && !checkBoxOnlyWeb.Checked;
            checkBoxRepackJs.Enabled = !checkBoxJavaOnlySwf.Checked && !checkBoxOnlyWeb.Checked;
            checkBoxSources.Enabled = !checkBoxJavaOnlySwf.Checked && !checkBoxOnlyWeb.Checked;
            checkBoxZip.Enabled = !checkBoxJavaOnlySwf.Checked && !checkBoxOnlyWeb.Checked;
        }

        private void checkBoxZip_CheckedChanged(object sender, EventArgs e)
        {
            if (checkBoxZip.Checked)
            {
                checkBoxBuildZip.Checked = (bool)checkBoxesState["checkBoxBuildZip"];
            }
            else
            {
                checkBoxesState["checkBoxBuildZip"] = checkBoxBuildZip.Checked;
                checkBoxBuildZip.Checked = false;
            }

            checkBoxBuildZip.Enabled = checkBoxZip.Checked;
        }

        private void checkBoxFlex_CheckedChanged(object sender, EventArgs e)
        {
            buttonBuild.Enabled = 
                checkBoxFlex.Checked || checkBoxPhp.Checked || checkBoxJava.Checked || checkBoxJavaOnlySwf.Checked || 
                checkBoxWeb.Checked || checkBoxOnlyWeb.Checked || checkBoxApps.Checked || checkBoxRepackJs.Checked;
        }
    }
}