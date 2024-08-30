# Ansible Setup Documentation

## MacOS

`brew install ansible`

## Windows WSL / Linux

Windows: Install WSL from the windows store (Ubuntu 18 not 20)

Install Python

Install ansible with pip: https://docs.ansible.com/ansible/latest/installation_guide/intro_installation.html#installing-ansible-with-pip

## Usage

using consul gather all the ip addresses of the pi devices and place them into as hosts file such as the following:

```
[raspis]
192.168.1.133 ansible_user={PI_USER}
192.168.1.158 ansible_user={PI_USER}
[server]
server-workstation ansible_host=xxx.xxx.x.xxx ansible_user=xxxx
```

Separating them into [raspis] and [server] allows us to send playbooks / commands to a subset of the hosts instead of all of them, wont want to be running a reboot command to the server.

initially we will be unable to connect to the pis as we have not set up the ssh access for the server, for this we will first need to install sshpass:

`https://gist.github.com/arunoda/7790979`

> may need to install x-code command line tools on MacOS

Once sshpass is installed, verify it by running the command `sshpass`

We now need to create the anisble playbook to allow the pi devices to connect to the system with ssh. Following we have ssh-key.yml

in the same directory this file is located we also require a pub_keys dir containing the public key of the server. if this is not yet generated it can be easily done by running `ssh-keygen`, the public key may then be stored in the  `~/.ssh/` dir, running the command `cp ~/.ssh/id_rsa.pub pub_keys/` should copy the public key into the pub_keys dir, if not check the location of id_rsa.pub and copy accordingly.

```
---
- name: Set authorized key for user root copying it from current user
  hosts: raspis
  become: true
  tasks:
  - name: Install ssh-key
    authorized_key:
      user: pi
      state: present
      key: "{{ lookup('file', item) }}"
    with_fileglob:
      - pub_keys/*.pub

```

Once all of the above is complete we are then able to run the command `ansible_playbook -k ssh-key.yml` it will then ask for an SSH Password, this will be the password to access the devices by default on raspberry pi the password is raspberry, this will be different however for security, it is however important they use the same password for this process.

Completing this should copy the public key over to each of the devices in the raspis group in the hosts file. The play recap should indicate ok=2 meaning ran successfully.

Once this is done we can test the ssh connection is stable using `ansible raspis -i hosts -m ping` This will ping the devices, if successful they are connected to the server through ssh.

If successful we are then able to create another playbook with two tasks, to update chromium and then reboot the pis.

```
---
- name: Update_Chromium
  hosts: raspis
  become: true
  tasks:
    - name: Update Chromium to latest version
      apt:
        name: chromium-browser
        state: latest
    - name: Reboot hosts
      command: reboot now
      ignore_errors: 'yes'
```
