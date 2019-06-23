namespace Builder
{
    partial class Builder
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(Builder));
            this.checkBoxApps = new System.Windows.Forms.CheckBox();
            this.checkBoxFlex = new System.Windows.Forms.CheckBox();
            this.checkBoxPhp = new System.Windows.Forms.CheckBox();
            this.buttonBuild = new System.Windows.Forms.Button();
            this.textBoxVersion = new System.Windows.Forms.TextBox();
            this.dateTimePicker = new System.Windows.Forms.DateTimePicker();
            this.checkBoxWeb = new System.Windows.Forms.CheckBox();
            this.checkBoxJava = new System.Windows.Forms.CheckBox();
            this.checkBoxZip = new System.Windows.Forms.CheckBox();
            this.checkBoxSources = new System.Windows.Forms.CheckBox();
            this.checkBoxJavaOnlySwf = new System.Windows.Forms.CheckBox();
            this.checkBoxBuildZip = new System.Windows.Forms.CheckBox();
            this.checkBoxRepackJs = new System.Windows.Forms.CheckBox();
            this.checkBoxOnlyWeb = new System.Windows.Forms.CheckBox();
            this.SuspendLayout();
            // 
            // checkBoxApps
            // 
            this.checkBoxApps.AutoSize = true;
            this.checkBoxApps.Checked = true;
            this.checkBoxApps.CheckState = System.Windows.Forms.CheckState.Checked;
            this.checkBoxApps.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.checkBoxApps.Location = new System.Drawing.Point(12, 150);
            this.checkBoxApps.Name = "checkBoxApps";
            this.checkBoxApps.Size = new System.Drawing.Size(71, 17);
            this.checkBoxApps.TabIndex = 5;
            this.checkBoxApps.Text = "AIR Apps";
            this.checkBoxApps.UseVisualStyleBackColor = true;
            this.checkBoxApps.CheckedChanged += new System.EventHandler(this.checkBoxFlex_CheckedChanged);
            // 
            // checkBoxFlex
            // 
            this.checkBoxFlex.AutoSize = true;
            this.checkBoxFlex.Checked = true;
            this.checkBoxFlex.CheckState = System.Windows.Forms.CheckState.Checked;
            this.checkBoxFlex.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.checkBoxFlex.Location = new System.Drawing.Point(12, 12);
            this.checkBoxFlex.Name = "checkBoxFlex";
            this.checkBoxFlex.Size = new System.Drawing.Size(107, 17);
            this.checkBoxFlex.TabIndex = 1;
            this.checkBoxFlex.Text = "Flex Components";
            this.checkBoxFlex.UseVisualStyleBackColor = true;
            this.checkBoxFlex.CheckedChanged += new System.EventHandler(this.checkBoxFlex_CheckedChanged);
            // 
            // checkBoxPhp
            // 
            this.checkBoxPhp.AutoSize = true;
            this.checkBoxPhp.Checked = true;
            this.checkBoxPhp.CheckState = System.Windows.Forms.CheckState.Checked;
            this.checkBoxPhp.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.checkBoxPhp.Location = new System.Drawing.Point(12, 35);
            this.checkBoxPhp.Name = "checkBoxPhp";
            this.checkBoxPhp.Size = new System.Drawing.Size(110, 17);
            this.checkBoxPhp.TabIndex = 2;
            this.checkBoxPhp.Text = "PHP Components";
            this.checkBoxPhp.UseVisualStyleBackColor = true;
            this.checkBoxPhp.CheckedChanged += new System.EventHandler(this.checkBoxFlex_CheckedChanged);
            // 
            // buttonBuild
            // 
            this.buttonBuild.Font = new System.Drawing.Font("Microsoft Sans Serif", 11F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.buttonBuild.Location = new System.Drawing.Point(10, 225);
            this.buttonBuild.Name = "buttonBuild";
            this.buttonBuild.Size = new System.Drawing.Size(369, 51);
            this.buttonBuild.TabIndex = 0;
            this.buttonBuild.Text = "Build";
            this.buttonBuild.UseVisualStyleBackColor = true;
            this.buttonBuild.Click += new System.EventHandler(this.buttonBuild_Click);
            // 
            // textBoxVersion
            // 
            this.textBoxVersion.Location = new System.Drawing.Point(11, 193);
            this.textBoxVersion.Name = "textBoxVersion";
            this.textBoxVersion.Size = new System.Drawing.Size(143, 20);
            this.textBoxVersion.TabIndex = 9;
            // 
            // dateTimePicker
            // 
            this.dateTimePicker.Location = new System.Drawing.Point(167, 193);
            this.dateTimePicker.MaxDate = new System.DateTime(3000, 12, 31, 0, 0, 0, 0);
            this.dateTimePicker.MinDate = new System.DateTime(1980, 1, 1, 0, 0, 0, 0);
            this.dateTimePicker.Name = "dateTimePicker";
            this.dateTimePicker.Size = new System.Drawing.Size(211, 20);
            this.dateTimePicker.TabIndex = 10;
            this.dateTimePicker.Value = new System.DateTime(2010, 10, 26, 14, 20, 57, 0);
            this.dateTimePicker.ValueChanged += new System.EventHandler(this.dateTimePicker1_ValueChanged);
            // 
            // checkBoxWeb
            // 
            this.checkBoxWeb.AutoSize = true;
            this.checkBoxWeb.Checked = true;
            this.checkBoxWeb.CheckState = System.Windows.Forms.CheckState.Checked;
            this.checkBoxWeb.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.checkBoxWeb.Location = new System.Drawing.Point(12, 104);
            this.checkBoxWeb.Name = "checkBoxWeb";
            this.checkBoxWeb.Size = new System.Drawing.Size(111, 17);
            this.checkBoxWeb.TabIndex = 4;
            this.checkBoxWeb.Text = "Web Components";
            this.checkBoxWeb.UseVisualStyleBackColor = true;
            this.checkBoxWeb.CheckedChanged += new System.EventHandler(this.checkBoxFlex_CheckedChanged);
            // 
            // checkBoxJava
            // 
            this.checkBoxJava.AutoSize = true;
            this.checkBoxJava.Checked = true;
            this.checkBoxJava.CheckState = System.Windows.Forms.CheckState.Checked;
            this.checkBoxJava.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.checkBoxJava.Location = new System.Drawing.Point(12, 58);
            this.checkBoxJava.Name = "checkBoxJava";
            this.checkBoxJava.Size = new System.Drawing.Size(111, 17);
            this.checkBoxJava.TabIndex = 3;
            this.checkBoxJava.Text = "Java Components";
            this.checkBoxJava.UseVisualStyleBackColor = true;
            this.checkBoxJava.CheckedChanged += new System.EventHandler(this.checkBoxFlex_CheckedChanged);
            // 
            // checkBoxZip
            // 
            this.checkBoxZip.AutoSize = true;
            this.checkBoxZip.Checked = true;
            this.checkBoxZip.CheckState = System.Windows.Forms.CheckState.Checked;
            this.checkBoxZip.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.checkBoxZip.Location = new System.Drawing.Point(167, 58);
            this.checkBoxZip.Name = "checkBoxZip";
            this.checkBoxZip.Size = new System.Drawing.Size(99, 17);
            this.checkBoxZip.TabIndex = 8;
            this.checkBoxZip.Text = "Create Zip Files";
            this.checkBoxZip.UseVisualStyleBackColor = true;
            this.checkBoxZip.CheckedChanged += new System.EventHandler(this.checkBoxZip_CheckedChanged);
            // 
            // checkBoxSources
            // 
            this.checkBoxSources.AutoSize = true;
            this.checkBoxSources.Checked = true;
            this.checkBoxSources.CheckState = System.Windows.Forms.CheckState.Checked;
            this.checkBoxSources.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.checkBoxSources.Location = new System.Drawing.Point(167, 35);
            this.checkBoxSources.Name = "checkBoxSources";
            this.checkBoxSources.Size = new System.Drawing.Size(115, 17);
            this.checkBoxSources.TabIndex = 12;
            this.checkBoxSources.Text = "Copy Source Code";
            this.checkBoxSources.UseVisualStyleBackColor = true;
            // 
            // checkBoxJavaOnlySwf
            // 
            this.checkBoxJavaOnlySwf.AutoSize = true;
            this.checkBoxJavaOnlySwf.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.checkBoxJavaOnlySwf.Location = new System.Drawing.Point(28, 81);
            this.checkBoxJavaOnlySwf.Name = "checkBoxJavaOnlySwf";
            this.checkBoxJavaOnlySwf.Size = new System.Drawing.Size(108, 17);
            this.checkBoxJavaOnlySwf.TabIndex = 13;
            this.checkBoxJavaOnlySwf.Text = "Create Only SWF";
            this.checkBoxJavaOnlySwf.UseVisualStyleBackColor = true;
            this.checkBoxJavaOnlySwf.CheckedChanged += new System.EventHandler(this.checkBoxJavaOnlySwf_CheckedChanged);
            // 
            // checkBoxBuildZip
            // 
            this.checkBoxBuildZip.AutoSize = true;
            this.checkBoxBuildZip.Checked = true;
            this.checkBoxBuildZip.CheckState = System.Windows.Forms.CheckState.Checked;
            this.checkBoxBuildZip.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.checkBoxBuildZip.Location = new System.Drawing.Point(167, 81);
            this.checkBoxBuildZip.Name = "checkBoxBuildZip";
            this.checkBoxBuildZip.Size = new System.Drawing.Size(116, 17);
            this.checkBoxBuildZip.TabIndex = 14;
            this.checkBoxBuildZip.Text = "Create Zip for Build";
            this.checkBoxBuildZip.UseVisualStyleBackColor = true;
            // 
            // checkBoxRepackJs
            // 
            this.checkBoxRepackJs.AutoSize = true;
            this.checkBoxRepackJs.Checked = true;
            this.checkBoxRepackJs.CheckState = System.Windows.Forms.CheckState.Checked;
            this.checkBoxRepackJs.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.checkBoxRepackJs.Location = new System.Drawing.Point(167, 12);
            this.checkBoxRepackJs.Name = "checkBoxRepackJs";
            this.checkBoxRepackJs.Size = new System.Drawing.Size(141, 17);
            this.checkBoxRepackJs.TabIndex = 15;
            this.checkBoxRepackJs.Text = "Repack JS Components";
            this.checkBoxRepackJs.UseVisualStyleBackColor = true;
            this.checkBoxRepackJs.CheckedChanged += new System.EventHandler(this.checkBoxFlex_CheckedChanged);
            // 
            // checkBoxOnlyWeb
            // 
            this.checkBoxOnlyWeb.AutoSize = true;
            this.checkBoxOnlyWeb.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.checkBoxOnlyWeb.Location = new System.Drawing.Point(28, 127);
            this.checkBoxOnlyWeb.Name = "checkBoxOnlyWeb";
            this.checkBoxOnlyWeb.Size = new System.Drawing.Size(107, 17);
            this.checkBoxOnlyWeb.TabIndex = 16;
            this.checkBoxOnlyWeb.Text = "Create Only Web";
            this.checkBoxOnlyWeb.UseVisualStyleBackColor = true;
            this.checkBoxOnlyWeb.CheckedChanged += new System.EventHandler(this.checkBoxJavaOnlySwf_CheckedChanged);
            // 
            // Builder
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(389, 287);
            this.Controls.Add(this.checkBoxOnlyWeb);
            this.Controls.Add(this.checkBoxRepackJs);
            this.Controls.Add(this.checkBoxBuildZip);
            this.Controls.Add(this.checkBoxJavaOnlySwf);
            this.Controls.Add(this.checkBoxSources);
            this.Controls.Add(this.checkBoxJava);
            this.Controls.Add(this.checkBoxZip);
            this.Controls.Add(this.dateTimePicker);
            this.Controls.Add(this.checkBoxPhp);
            this.Controls.Add(this.checkBoxWeb);
            this.Controls.Add(this.textBoxVersion);
            this.Controls.Add(this.buttonBuild);
            this.Controls.Add(this.checkBoxFlex);
            this.Controls.Add(this.checkBoxApps);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.Location = new System.Drawing.Point(100, 130);
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.Name = "Builder";
            this.StartPosition = System.Windows.Forms.FormStartPosition.Manual;
            this.Text = "Builder";
            this.FormClosing += new System.Windows.Forms.FormClosingEventHandler(this.Form1_FormClosing);
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion
        private System.Windows.Forms.CheckBox checkBoxApps;
        private System.Windows.Forms.CheckBox checkBoxFlex;
        private System.Windows.Forms.CheckBox checkBoxPhp;
        private System.Windows.Forms.Button buttonBuild;
        private System.Windows.Forms.TextBox textBoxVersion;
        private System.Windows.Forms.DateTimePicker dateTimePicker;
        private System.Windows.Forms.CheckBox checkBoxWeb;
        private System.Windows.Forms.CheckBox checkBoxJava;
        private System.Windows.Forms.CheckBox checkBoxZip;
        private System.Windows.Forms.CheckBox checkBoxSources;
        private System.Windows.Forms.CheckBox checkBoxJavaOnlySwf;
        private System.Windows.Forms.CheckBox checkBoxBuildZip;
        private System.Windows.Forms.CheckBox checkBoxRepackJs;
        private System.Windows.Forms.CheckBox checkBoxOnlyWeb;
    }
}

