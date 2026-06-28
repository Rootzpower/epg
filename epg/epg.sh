# Based on the original M3UPT project by LITUATUI.
# See: https://github.com/LITUATUI/M3UPT

#!/bin/bash

cd /home/runner/work/epg/epg/iptv-org-epg && npm install

# MAIN EPG
npm run grab --- --channels=../epg/channels.xml --output=../epg/epg-main.xml --days=7 --maxConnections=20

# ╔══════════════════════════════════════════════════════════╗
# ║                  📺 EPG NACIONAIS 📺🇵🇹                     ║
# ╚══════════════════════════════════════════════════════════╝

# MEO - https://www.meo.pt/tv/canais-programacao/guia-tv
npm run grab --- --sites=meo.pt --output=../epg/epg-meo-pt.xml --days=7 --maxConnections=20

# NOS - https://nostv.pt/guia/
npm run grab --- --sites=nostv.pt --output=../epg/epg-nos-pt.xml --days=7 --maxConnections=20

# RTP - https://www.rtp.pt/tv/
npm run grab --- --sites=rtp.pt --output=../epg/epg-rtp-pt.xml --days=7 --maxConnections=20

# SIC - https://opto.sic.pt/guia-tv
npm run grab --- --sites=opto.sic.pt --output=../epg/epg-sic-pt.xml --days=7 --maxConnections=20

# TVI - https://tvi.iol.pt/guiatv
npm run grab --- --sites=tvi.iol.pt --output=../epg/epg-tvi-pt.xml --days=7 --maxConnections=20

# VODAFONE - https://www.vodafone.pt/
npm run grab --- --sites=vodafone.pt --output=../epg/epg-vodafone-pt.xml --days=7 --maxConnections=20

# ╔══════════════════════════════════════════════════════════╗
# ║                 🌍 EPG INTERNACIONAIS 🌍                ║
# ╚══════════════════════════════════════════════════════════╝

# NOW TV - https://nowplayer.now.com/tvguide
npm run grab --- --sites=nowplayer.now.com  --lang=en --output=../epg/epg-nowplayer-now-en-com.xml --days=7 --maxConnections=20

# ORANGETV - https://orangetv.orange.es/epg
npm run grab --- --sites=orangetv.orange.es --output=../epg/epg-orangetv-orange-es.xml --days=7 --maxConnections=20

# VIVO PLAY - https://www.vivoplay.com.br/tv-guide/epg
npm run grab --- --sites=vivoplay.com.br --output=../epg/epg-vivoplay-br.xml --days=7 --maxConnections=20

# WHALE TV+ - https://watch.whaletvplus.com/
npm run grab --- --sites=watch.whaletvplus.com --output=../epg/epg-watch-whaletvplus-com.xml --days=7 --maxConnections=20

#-------------------------------------------------------------

# Compress epg xml files only for gz format
cd ../epg
gzip -k -f -9 epg*.xml

# Remove orphan epg xml and xz files
rm -f epg*.xml epg*.xml.xz

exit 0
