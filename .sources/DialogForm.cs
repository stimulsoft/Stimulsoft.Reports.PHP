using System;
using System.Windows.Forms;
using System.Runtime.InteropServices;

namespace Builder
{
    public partial class DialogForm : Form
    {
        [DllImport("user32.dll")]
        private static extern bool FlashWindow(IntPtr hwnd, bool bInvert);

        public void Clear()
        {
            stiTextBox1.Clear();
        }

        public void AppendLine(string str)
        {
            if (Visible) stiTextBox1.AppendText(str + "\r\n");
            Application.DoEvents();
        }

        public void Append(string str)
        {
            if (Visible) stiTextBox1.AppendText(str);
            Application.DoEvents();
        }

        private void button1_Click(object sender, EventArgs e)
        {
            Close();
        }

        public void flashForm()
        {
            FlashWindow(this.Handle, false);
        }

        public DialogForm()
        {
            InitializeComponent();
        }
    }
}
