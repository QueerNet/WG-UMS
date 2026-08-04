"use strict";

async function callVpnApi(method, params = {}) {
    const response = await fetch('/app/classes/VPN/api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ method, ...params })
    });
    if (!response.ok) {
        throw new Error(`Server returned ${response.status}`);
    }
    return response.json();
}



async function addDevice (userid, devname, pubkey, endpt) {
    if (devname==="") {
        return;
    } else {
        // Debug tracer
        console.log(devname);

        // Generate keypair
        let keyPair = wireguard.generateKeypair();
        let publicKey = keyPair['publicKey'];
        let privateKey = keyPair['privateKey'];
        let psk = keyPair['presharedKey'];

        // Fetch next available
        const result = await callVpnApi('wg_get_next_ip', { userid: userid, devname: devname });
        let allowedIp = result['ip'];

        // Generate configuration

        
        let confArr = [
        "[Interface]\r\n",
        "Address = " + allowedIp.replace('/32', '/24') + "\r\n",
        "PrivateKey = " + privateKey + "\r\n",
        "DNS = 10.200.200.1\r\n",
        "[Peer]\r\n",
        "PublicKey = "+ pubkey + "\r\n",
        "PresharedKey = "+ psk + "\r\n",
        "AllowedIPs = 0.0.0.0/0, ::0/0\r\n",
        "Endpoint = "+ endpt +":42069\r\n"];

        var confBlob = new Blob(confArr);

        let configuration = confArr.join('');
        

        // Create an eventlistener for "Save" button to trigger "saveDevice"
        // (For now, just hard-coded)
        const result_add_peer = await callVpnApi('wg_add_peer', { iface: 'QLS', pubkey: publicKey, psk: psk, allowedIp: allowedIp });

        console.log(configuration);

        // Remove placeholder
        document.getElementById("qrcode").className="";
        document.getElementById("downloadConf").className="btn btn-primary";

        document.getElementById('downloadConf').addEventListener("click", function (e) {
            triggerBlobDownload(confBlob, devname + '.conf');
        });

        // Display QR
        new QRCode(document.getElementById("qrcode"), configuration);
        
        // Show error or success message
        console.log(result_add_peer)
    }
}

async function rmDevice (id) {
    const result = await callVpnApi('wg_rm_peer', { iface: 'QLS', devid: id });
    console.log(result);
    window.location.reload();
}

function triggerBlobDownload(fileBlob, fileName) {
  const tempUrl = URL.createObjectURL(fileBlob)
  const anchor = document.createElement('a')

  anchor.href = tempUrl
  anchor.download = fileName
  anchor.style.display = 'none'

  document.body.append(anchor)
  anchor.click()
  anchor.remove()

  setTimeout(() => {
    URL.revokeObjectURL(tempUrl)
  }, 1000)
}