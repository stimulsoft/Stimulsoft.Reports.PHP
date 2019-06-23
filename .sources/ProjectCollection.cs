using System.Collections;

namespace Builder
{
    class ProjectCollection : CollectionBase
    {
        private Project mainProject;

        public void addProject(Project project)
        {
            addProject(project, true);
        }

        public void addProject(Project project, bool append)
        {
            List.Add(project);
            if (mainProject != null && append) mainProject.Appends.addProject(project, false);
        }

        public ArrayList getSortedProject()
        {
            ArrayList compilingProject = new ArrayList();

            foreach (Project project in this)
            {
                if (project.Selected == true && project.Compiling == false)
                {
                    project.Compiling = true;
                    compilingProject.AddRange(project.DependsOn.getSortedProject());
                    compilingProject.Add(project);
                }
            }

            return compilingProject;
        }

        public void clearSelected()
        {
            foreach (Project project in this)
            {
                project.Selected = false;
            }
        }

        public void clearCompiling()
        {
            foreach (Project project in this)
            {
                project.Compiling = false;
            }
        }

        public Project this[int index]
        {
            get
            {
                return (Project)List[index];
            }
        }

        public Project this[string name]
        {
            get
            {
                foreach (Project project in List)
                {
                    if (project.ProjectName == name) return project;
                }
                return null;
            }
        }

        
        #region Constructors
        public ProjectCollection()
        {
        }

        public ProjectCollection(Project project)
        {
            this.mainProject = project;
        }
        #endregion
    }
}
