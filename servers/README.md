HOW TO SET UP WORKERS using systemctl

Important gotcha:  All paths in a worker should be absolute!

src: https://serverfault.com/questions/730239/start-n-processes-with-one-systemd-service-file


$ sudo nano /etc/systemd/system/abrupdate@.service

[Unit]
Description=Websocket Server %i

[Service]
ExecStart=/usr/bin/php /var/www/staging.phippsy.tech/ndismate/servers/websocket/websocket.php
StandardOutput=null
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target


############

sudo systemctl enable abrupdate\@{1..10}.service

sudo systemctl daemon-reload

Then I can use (start|restart|stop|status) like this
sudo systemctl start abrupdate\@{1..10}.service
