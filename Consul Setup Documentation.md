# Consul Setup Documentation

## Windows

In Powershell export a couple environment variables to specify the Consul download base URL and preferred Consul version for convenience and concise commands.

`$CONSUL_VERSION = '1.8.0'`

`$CONSUL_URL = 'https://releases.hashicorp.com/consul'`

`$CONSUL_DIR = "C:/Hashicorp/Consul"`

Create the Consul directories

`mkdir "$CONSUL_DIR"`

`mkdir "$CONSUL_DIR/data"`

`mkdir "$CONSUL_DIR/certs"`

Change into the Consul directories and download the Consul package and SHA256 summary files.

`Set-Location "$CONSUL_DIR"`

`Invoke-WebRequest "${CONSUL_URL}/${CONSUL_VERSION}/consul_${CONSUL_VERSION}_windows_amd64.zip" -Outfile consul_${CONSUL_VERSION}_windows_amd64.zip`

`Invoke-WebRequest "${CONSUL_URL}/${CONSUL_VERSION}/consul_${CONSUL_VERSION}_SHA256SUMS" -Outfile consul_${CONSUL_VERSION}_SHA256SUMS`

`Invoke-WebRequest "${CONSUL_URL}/${CONSUL_VERSION}/consul_${CONSUL_VERSION}_SHA256SUMS.sig" -Outfile consul_${CONSUL_VERSION}_SHA256SUMS.sig`

Check the hashes to make sure you have valid files.

`get-content "${CONSUL_DIR}/*SHA256SUMS"| select-string  (get-filehash -algorithm SHA256 "${CONSUL_DIR}/consul_${CONSUL_VERSION}_windows_amd64.zip").hash.toLower()`

Expand out the zip file in your directory

`Expand-Archive -Confirm:$false -Force:$true "${CONSUL_DIR}/consul_${CONSUL_VERSION}_windows_amd64.zip" "$CONSUL_DIR"`

Add Consul directory to the system path (both current and future).

`$env:path += ";${CONSUL_DIR}"`

After installing Consul, verify that the installation worked by opening a new terminal session and running the command `consul`.


## Linux

First, export a couple environment variables to specify the Consul download base URL and preferred Consul version for convenience and concise commands. Then use curl to download the package and SHA256 summary files.

`export CONSUL_VERSION="1.8.0"`

`export CONSUL_URL="https://releases.hashicorp.com/consul"`

At this point head to the 'CONSUL_URL' above just to make sure you are downloading the correct zip file for your system, the below link is great for some versions of linux but the raspberry pi 4 version will be, for example : `consul_${CONSUL_VERSION}_linux_armhfv6.zip` so this would be switched in the following command.

`curl --silent --remote-name \
  ${CONSUL_URL}/${CONSUL_VERSION}/consul_${CONSUL_VERSION}_linux_amd64.zip`

`curl --silent --remote-name \
  ${CONSUL_URL}/${CONSUL_VERSION}/consul_${CONSUL_VERSION}_SHA256SUMS`

`curl --silent --remote-name \
  ${CONSUL_URL}/${CONSUL_VERSION}/consul_${CONSUL_VERSION}_SHA256SUMS.sig`

Unzip the downloaded package and move the consul binary to /usr/bin/. Check consul is available on the system path.

`unzip consul_${CONSUL_VERSION}_linux_amd64.zip`

`sudo chown root:root consul`

`sudo mv consul /usr/bin/`

`consul --version`

The consul command features opt-in autocompletion for flags, subcommands, and arguments (where supported). Enable autocompletion.

`consul -autocomplete-install`

`complete -C /usr/bin/consul consul`

Create a unique, non-privileged system user to run Consul and create its data directory.

`sudo useradd --system --home /etc/consul.d --shell /bin/false consul`

`sudo mkdir --parents /opt/consul`

`sudo chown --recursive consul:consul /opt/consul`

After installing Consul, verify that the installation worked by opening a new terminal session and running the command `consul`.

## MacOS

Homebrew is a free and open-source package management system for Mac OS X. Install the Consul formula from the terminal.

`brew install consul`

After installing Consul, verify that the installation worked by opening a new terminal session and running the command `consul`.

## Prepare the security credentials

### Generate the gossip encryption key

Gossip is encrypted with a symmetric key, since gossip between nodes is done over UDP. All agents must have the same encryption key.

You can create the encryption key via the Consul CLI even though no Consul agents are running yet. Generate the encryption key.

`consul keygen`

The encryption key will be plain text output such as `qDOPBEr+/oUVeOFQOnVypxwDaHzLrD+lvjo5vCEBbZ0=`

> ***NOTE You will need to add the newly generated key to the encrypt option in the server configuration on all Consul agents. Save your key in a safe location. You will need to reference the key throughout the installation.***

## Generate TLS certificates for RPC encryption

Consul can use TLS to verify the authenticity of servers and clients. To enable TLS, Consul requires that all agents have certificates signed by a single Certificate Authority (CA).

In this tutorial, you will use Consul to create a CA for your certificates. For in-depth information about setting up TLS certificates, review the [Secure Consul Agent Communication with TLS Encryption][1] tutorial:

### Create the Certificate Authority

Start by creating the CA on your admin instance, using the Consul CLI.

`consul tls ca create`

### Create the certificates

Next create a set of certificates, one for each Consul agent. You will need to select a name for your primary datacenter now, so that the certificates are named properly.

First, for your Consul servers, use the following command to create a certificate for each server. The file name increments automatically.

`consul tls cert create -server -dc <dc_name>`

Use the following command with the -client flag to create client certificates. The file name increments automatically.

`consul tls cert create -client -dc <dc_name>`

### Distribute the certificates to agents
You must distribute the CA certificate, consul-agent-ca.pem, to each of the Consul agents as well as the agent specific certificate and private key.

Below is an SCP example which will send the CA certificate, agent certificate and private key to the IP address you specify, and put it into the /etc/consul.d/ directory.

`scp consul-agent-ca.pem <dc-name>-<server/client>-consul-<cert-number>.pem <dc-name>-<server/client>-consul-<cert-number>-key.pem <USER>@<PUBLIC_IP>:/etc/consul.d/`

## Configure Consul agents

Consul uses documented reasonable defaults so only non-default values must be set in the configuration file. Configuration can be read from multiple files and is loaded in lexical order. Check the full description for more information about configuration loading and merge semantics.

Consul server agents typically require a superset of configuration required by Consul client agents. You will specify common configuration used by all Consul agents in consul.hcl and server specific configuration in server.hcl.

## Raspberry Pi Agent Configuration

`sudo mkdir --parents /etc/consul.d`

`sudo touch /etc/consul.d/consul.hcl`

`sudo chown --recursive consul:consul /etc/consul.d`

`sudo chmod 640 /etc/consul.d/consul.hcl`

Add this configuration to the consul.hcl configuration file, The retry_join parameter allows you to configure all Consul agents to automatically form a datacenter using a common Consul server accessed via DNS address, IP address or using Cloud Auto-join. This removes the need to manually join the Consul datacenter nodes together.:

```
server = false
datacenter = "dc1"
data_dir = "/opt/consul"
encrypt = "qDOPBEr+/oUVeOFQOnVypxwDaHzLrD+lvjo5vCEBbZ0="
ca_file = "/etc/consul.d/consul-agent-ca.pem"
cert_file = "/etc/consul.d/dc1-client-consul-0.pem"
key_file = "/etc/consul.d/dc1-client-consul-0-key.pem"
verify_incoming = true
verify_outgoing = true
verify_server_hostname = true

retry_join = ["192.168.1.188"]
```

- datacenter - The datacenter in which the agent is running.
- data_dir - The data directory for the agent to store state.
- encrypt - Specifies the gossip encryption key to use for Consul network traffic. Generated above.
- ca_file - Specifies the path to the CA public certificate file.
- cert_file - Specifies the path to the agents public certificate file.
- key_file - Specifies the path to the agents certificate private key file.
- verify_incoming - If set to true, Consul requires all incoming connections to use TLS.
- verify_outgoing - If set to true, Consul requires that all outgoing connections from this agent use TLS.
- verify_server_hostname - If set to true, Consul verifies for all outgoing TLS connections that the TLS certificate presented by the servers matches server.<datacenter>.<domain> hostname.

## Consul Server Configuration

### Windows

Create a configuration file at C:/Hashicorp/Consul/server.hcl.

`New-Item -type file -Path "$CONSUL_DIR" -name server.hcl`

Add this configuration to the server.hcl configuration file:

```
server=true
bind_addr="0.0.0.0"
ui=true
datacenter="dc1"
data_dir="{{DIR}}/consul/data"
bootstrap=true
log_file="{{DIR}}/consul/consul.log"
encrypt="qDOPBEr+/oUVeOFQOnVypxwDaHzLrD+lvjo5vCEBbZ0="
node_name="raspis_server"
ca_file="{{DIR}}/consul/consul-agent-ca.pem"
cert_file="{{DIR}}/consul/dc1-server-consul-0.pem"
key_file="{{DIR}}/consul/dc1-server-consul-0-key.pem"

auto_encrypt {
  allow_tls = true
}
```

To run the server manually run the following command:

`consul agent -server -config-file="C:/Hashicorp/Consul/server.hcl"`

### Linux

Create a configuration file at /etc/consul.d/server.hcl:

`sudo touch /etc/consul.d/server.hcl`

`sudo chown --recursive consul:consul /etc/consul.d`

`sudo chmod 640 /etc/consul.d/server.hcl`

Add this configuration to the server.hcl configuration file:

```
server=true
bind_addr="0.0.0.0"
ui=true
datacenter="dc1"
data_dir="/opt/consul/"
bootstrap=true
log_file="{{DIR}}/consul/consul.log"
encrypt="qDOPBEr+/oUVeOFQOnVypxwDaHzLrD+lvjo5vCEBbZ0="
node_name="raspis_server"
ca_file="{{DIR}}/consul/consul-agent-ca.pem"
cert_file="{{DIR}}/consul/dc1-server-consul-0.pem"
key_file="{{DIR}}/consul/dc1-server-consul-0-key.pem"

auto_encrypt {
  allow_tls = true
}
```

To run the server manually run the following command:

`consul agent -server -config-file="/etc/consul.d/server.hcl"`

## Configure Consul Process

### Windows - Configure Consul Service

Generate a random password for your Consul service account.

`$password = (-join ((0x30.. 0x39) + ( 0x41.. 0x5A) + ( 0x61.. 0x7A) | Get-Random -Count 16 | % {[char]$_}))`

`$securePassword = (ConvertTo-SecureString -AsPlainText -Force -String $password)`


Create a unique, non-privileged system user to run consul.

`New-LocalUser "consul" -FullName "Consul User" -Description "Consul Service Account" -Password $securePassword`

`$Credential = New-Object -TypeName System.Management.Automation.PSCredential(".\consul", $securePassword)`

Create Consul service

`New-Service -Name Consul -BinaryPathName "${CONSUL_DIR}/consul.exe agent -config-dir=${CONSUL_DIR}"  -DisplayName Consul -Description "Hashicorp Consul Service https://consul.io" -StartupType "Automatic"`

Check that your configuration file is valid, with the Consul CLI validate command.

`consul validate /Hashicorp/consul/server.hcl`

### MacOS - Create an automation

Open the automator app, create a new `Run Shell Script` automation

enter the following code, making sure the consul and config file locations are correct, save it in a location it will not get deleted. The killall command prevents an infinite spinning gear icon in the toolbar as it is an infinite scriptps

```
killall ScriptMonitor
/usr/local/bin/consul agent -server -config-file="/etc/consul.d/server.hcl" &
```


navigate to `Users & Groups` in System Preferences, select the user that will be logged in, navigate to login items, click add new, locate the automator file just saved and select it. Now on login the server will run automatically.

### Linus -  Configure systemd

Systemd uses [documented reasonable defaults][2] so only non-default values must be set in the configuration file.


Create a Consul service file at /usr/lib/systemd/system/consul.service.

`sudo touch /usr/lib/systemd/system/consul.service`

Add this configuration to the Consul service file. [Click here for more information about the parameters][3]

```
[Unit]
Description="HashiCorp Consul - A service mesh solution"
Documentation=https://www.consul.io/
Requires=network-online.target
After=network-online.target
ConditionFileNotEmpty=/etc/consul.d/consul.hcl

[Service]
Type=notify
User=consul
Group=consul
ExecStart=/usr/bin/consul agent -config-dir=/etc/consul.d/
ExecReload=/bin/kill --signal HUP $MAINPID
KillMode=process
KillSignal=SIGTERM
Restart=on-failure
LimitNOFILE=65536

[Install]
WantedBy=multi-user.target
```
Check that your configuration file is valid, with the Consul CLI validate command.

`consul validate /etc/consul.d/server.hcl`






[1]:https://learn.hashicorp.com/tutorials/consul/tls-encryption-secure
[2]:https://www.freedesktop.org/software/systemd/man/systemd.directives.html
[3]:https://learn.hashicorp.com/tutorials/consul/deployment-guide
