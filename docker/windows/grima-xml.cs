using System.Diagnostics;
using System.Xml.Linq;
using System;

public class Program {

  private class QuickRun {
    public Process process;
    public string stdout;
    public string stderr;
    public bool ran;
    public Exception exc;
    public QuickRun( string exe, string arguments ) {
      process = new Process();
      process.StartInfo.FileName = exe;
      process.StartInfo.Arguments = arguments;
      process.StartInfo.UseShellExecute = false;
      process.StartInfo.RedirectStandardOutput = true;
      process.StartInfo.RedirectStandardError = true;
      try {
        process.Start();
        ran = true;
      } catch(Exception x) {
        exc = x;
        ran = false;
        return;
      }
      Console.WriteLine("\nRan: " + exe + " " + arguments );
      stdout = process.StandardOutput.ReadToEnd().TrimEnd( '\r', '\n' );
      stderr = process.StandardError.ReadToEnd().TrimEnd( '\r', '\n' );
      if (stdout!="") Console.WriteLine("Out: " + stdout.Replace("\n","\n...: ") );
      if (stderr!="") Console.WriteLine("Err: " + stderr.Replace("\n","\n...: ") );
      process.WaitForExit();
      Console.WriteLine("");
    }
  }

  private static string server = "";
  private static string apikey = "";
  private static string dockerImageId = "";
  private static string dockerImageTag = "";
  private static bool dockerInstalled;

  private static void HandleConfig(string file) {
    Console.WriteLine("Handling config from " + file);
    /**********************************************************************
     *
     *  Read config file, create if necessary, ask for values if necessary,
     *  save changed config if necessary.
     */
    Boolean dirty = !System.IO.File.Exists(file);
    XElement doc = dirty ? new XElement("grima") : XElement.Load(file);
    try { server = doc.Element("server").Value; } catch { }
    try { apikey = doc.Element("apikey").Value; } catch { }
    try { dockerImageId = doc.Element("dockerImageId").Value; } catch { }
    try { dockerImageTag = doc.Element("dockerImageTag").Value; } catch { }
    if(server=="") {
      Console.WriteLine("Asking for the Alma Server address (in a message box)");
      server = Microsoft.VisualBasic.Interaction.InputBox(
        "Enter your Alma Server's web address",
        "Alma Server",
        "https://api-na.hosted.exlibrisgroup.com");
      dirty = true;
    }
    if(apikey=="") {
      Console.WriteLine("Asking for the Alma API key (in a message box)");
      apikey = Microsoft.VisualBasic.Interaction.InputBox(
        "Enter your Alma API Key",
        "Alma API Key",
        "");
      dirty = true;
    }
    if(dockerImageTag=="") {
      Console.WriteLine("Asking which Docker Image to use (in a message box)");
      dockerImageTag = Microsoft.VisualBasic.Interaction.InputBox(
        "Which docker image should we get grima updates from?",
        "Docker Image Tag",
        "zemkat/grima");
      dirty = true;
      if (dockerImageTag!="") {
        QuickRun qr = new QuickRun("docker.exe","pull " + dockerImageTag);
      }
    }
    if (dockerImageId=="") {
      QuickRun qr = new QuickRun("docker.exe","image ls --format {{.ID}} " + dockerImageTag);
      dockerImageId = qr.stdout;
      dirty = true;
    }
    Console.WriteLine("Server: " + server);
    Console.WriteLine("APIKey: " + apikey);
    Console.WriteLine("Docker: " + dockerImageTag);
    Console.WriteLine("DockID: " + dockerImageId);
    if (dirty) {
      doc.SetElementValue("server",server);
      doc.SetElementValue("apikey",apikey);
      doc.SetElementValue("dockerImageTag",dockerImageTag);
      doc.SetElementValue("dockerImageId",dockerImageId);
      doc.Save(file);
    }
  }

  private static void UpdateContainer() {
    QuickRun qr = new QuickRun( "docker.exe", "pull " + dockerImageTag );
    qr = new QuickRun( "docker.exe", "image ls --format {{.ID}} " + dockerImageTag );
    if (qr.stdout != dockerImageId) {
      Console.WriteLine("New grima available!");
    }
  }

  private static void StartContainer() {
    HandleConfig("grimaConf.xml");
    UpdateContainer();
    Console.WriteLine("Starting docker container");
    new QuickRun("docker.exe","tag " + dockerImageId + " local/grima");
    new QuickRun("docker.exe",
      "run " +
      "--detach " +
      "--env apikey=" + apikey + " " +
      "--env server=" + server + " " +
      "--name grima " +
      "--publish 127.0.0.1:19290-30000:19290 " +
      "--rm " +
      "local/grima" );
  }

  private static string GetPort() {
    Console.WriteLine("Finding docker port");
    QuickRun qr = new QuickRun("docker.exe","port grima");
    dockerInstalled = qr.ran;
    if (!dockerInstalled) {
      return "";
    }
    try {
      string addr = qr.stdout.Split(' ')[2];
      return addr.Replace("0.0.0.0","127.0.0.1"); // not currently needed, but fixes --publish-all
    } catch {
      return "";
    }
  }

  public static void Main(string[] args) {
    string addr = GetPort();
    if ( (addr == "") && dockerInstalled ) {
      StartContainer();
      addr = GetPort();
    }
    if (addr != "") {
      Console.WriteLine("Opening grima in default browser");
      Process.Start( "http://" + addr ).WaitForExit();
    } else if (dockerInstalled) {
      Console.WriteLine("Can't find what address grima is running on. Probably not running?");
    } else {
      Console.WriteLine("It doesn't look like docker is installed. You'll need to do that first.");
      Process.Start( "https://docs.docker.com/docker-for-windows/install/" );
    }
    Console.WriteLine("Done. Press return to close this window.");
    Console.ReadLine();
  }
}
