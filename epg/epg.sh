# Based on the original M3UPT project by LITUATUI.
# See: https://github.com/LITUATUI/M3UPT

#!/bin/bash

cd /home/runner/work/epg/epg/iptv-org-epg && npm install

# Main epg
npm run grab --- --channels=../epg/channels.xml --output=../epg/epg-main.xml --days=7 --maxConnections=20

# RTP epg
npm run grab --- --sites=rtp.pt --output=../epg/epg-rtp-pt.xml --days=7 --maxConnections=20

# SIC epg
npm run grab --- --sites=opto.sic.pt --output=../epg/epg-sic-pt.xml --days=7 --maxConnections=20

# TVI epg
npm run grab --- --sites=tvi.iol.pt --output=../epg/epg-tvi-pt.xml --days=7 --maxConnections=20

# Vodafone PT epg
npm run grab --- --sites=vodafone.pt --output=../epg/epg-vodafone-pt.xml --days=7 --maxConnections=20

# Nos epg
npm run grab --- --sites=nostv.pt --output=../epg/epg-nos-pt.xml --days=7 --maxConnections=20

# Vivo Play epg
npm run grab --- --sites=vivoplay.com.br --output=../epg/epg-vivoplay-br.xml --days=7 --maxConnections=20

# orangetv.es
npm run grab --- --sites=orangetv.orange.es --output=../epg/epg-orangetv-orange-es.xml --days=7 --maxConnections=20

# watch.whaletvplus.com
npm run grab --- --sites=watch.whaletvplus.com --output=../epg/epg-watch-whaletvplus-com.xml --days=7 --maxConnections=20

# nowplayer.now.com
npm run grab --- --sites=nowplayer.now.com  --lang=en --output=../epg/nowplayer.now.com_en.channels.xml --days=7 --maxConnections=20

# Compress epg xml files only for *.gz format
cd ../epg
gzip -k -f -9 epg*.xml

# Remove epg xml files
rm -f epg*.xml epg*.xml.xz

exit 0