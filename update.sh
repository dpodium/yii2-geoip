newsha=$(curl "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&license_key=Urbg1xsxwde8bdlQ&suffix=tar.gz.sha256")
echo $i
oldsha=$(cat "components/db/sha256")

if [ "$newsha" == "$oldsha" ]; then
    echo "No update."
    exit
fi

echo "Update found."
wget -O "components/db/db.tar.gz" "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&license_key=Urbg1xsxwde8bdlQ&suffix=tar.gz"
tar --strip-components=1 -zxvf "components/db/db.tar.gz" -C "components/db/"
rm -f "components/db/db.tar.gz"
git commit -am "db update"
echo "$newsha" > "components/db/sha256"
echo ""
echo ""
echo ""
echo ""
echo ""
echo "----------"
echo "New update, please tag and push"
echo "----------"
echo ""
echo ""
echo ""
echo ""
echo ""