# Installation
If the software is run on Raspberry Pi OS, the file buderus.service has to be copied to /etc/systemd/system .
Enable the the Service via

    systemctl enable buderus

After that, the Service can be started via

    service buderus start
