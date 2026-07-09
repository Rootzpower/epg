╔════════════════════════════════════════════════════════════╗
║        📺 VODAFONE TV - CHANNEL LIST EXPORT GUIDE 📺      ║
╚════════════════════════════════════════════════════════════╝

  🎯GOAL:
	Export full Vodafone TV channel list as a JSON file,
	with a single click. (name, epgId, channel number) 
	
╔════════════════════════════════════════════════════════════╗
║                     🔧 ONE-TIME SETUP 🔧                  ║
╚════════════════════════════════════════════════════════════╝

	1. Create a new bookmark
	- Edge: Ctrl+D on any page
	- Name it:  Vodafone Channels JSON
	2. In the URL field of the bookmark, paste the code below
   (the whole thing, including "javascript:" at the start)
	3. Save the bookmark.

╔════════════════════════════════════════════════════════════╗
║                   💻 BOOKMARKLET CODE 💻                  ║
╚════════════════════════════════════════════════════════════╝

javascript:(function () {
    var data = JSON.parse(localStorage.getItem("xclient-channels")).channels;
    var blob = new Blob([JSON.stringify(data, null, 2)], {
        type: "application/json"
    });
    var a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "vodafone-channels.json";
    a.click();
    URL.revokeObjectURL(a.href);
})();

╔════════════════════════════════════════════════════════════╗
║              🔄 EVERY TIME YOU WANT FRESH DATA 🔄         ║
╚════════════════════════════════════════════════════════════╝

  Step 1  ->  Open Edge
  Step 2  ->  Go to tv.vodafone.pt/#/watchtv
              (let the channel grid load)
  Step 3  ->  Click the "Vodafone Channels JSON" bookmark
  Step 4  ->  vodafone-channels.json downloads automatically

			No F12, no console, no copy-pasting.

╔════════════════════════════════════════════════════════════╗
║    🛠️  ALTERNATIVE: MANUAL METHOD (DevTools console) 🛠️   ║
╚════════════════════════════════════════════════════════════╝

  If you prefer not to use a bookmark, or the bookmarklet
  ever stops working, you can run the same code by hand:

  Step 1  ->  Open Edge
  Step 2  ->  Go to tv.vodafone.pt/#/watchtv
              (let the channel grid load)
  Step 3  ->  Press F12 to open DevTools
  Step 4  ->  Click the "Console" tab
  Step 5  ->  Paste the code below and press Enter
  Step 6  ->  vodafone-channels.json downloads automatically

╔════════════════════════════════════════════════════════════╗
║     ⌨️  CONSOLE CODE (without "javascript:" prefix) ⌨️    ║
╚════════════════════════════════════════════════════════════╝

var data = JSON.parse(localStorage.getItem("xclient-channels")).channels;
var blob = new Blob([JSON.stringify(data, null, 2)], {
    type: "application/json"
});
var a = document.createElement("a");
a.href = URL.createObjectURL(blob);
a.download = "vodafone-channels.json";
a.click();
URL.revokeObjectURL(a.href);

╔════════════════════════════════════════════════════════════╗
║              📅 RECOMMENDED REFRESH CYCLE 📅              ║
╚════════════════════════════════════════════════════════════╝
  Run it monthly to catch new/removed channels and keep
  your XMLTV guide up to date.
╔════════════════════════════════════════════════════════════╗
║                         💡 TIP 💡                         ║
╚════════════════════════════════════════════════════════════╝
  Pin the bookmark to your favorites bar for one-click
  access.