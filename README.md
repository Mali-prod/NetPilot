# NET PILOT - Simple RouterOS SDN Controller



A simple Laravel WebUI for controlling devices using RouterOS via their REST API.

Main template is [Star Admin 2 by BootstrapDash](https://demo.bootstrapdash.com/star-admin2-free/template/index.html)

Login is based on [Finance Mobile Application-UX/UI Design Screen One login page](https://codepen.io/sowg/pen/qBXjXoE) (slight background changes)

## Features:

- View device interfaces (Physical/Virtual/WiFi)

- Bridge configuration

- Security Profile & WiFi configuration

- Address configuration

- Configuration for an assortment of services

    - DHCP

    - DNS

    - Wireguard

## Modules used:

- [**DataTables**](https://datatables.net/) - Searching, pagination, sorting

- [**SweetAlert**](https://sweetalert2.github.io/) - JS Pop-Up messages

- [**Toastr**](https://www.jqueryscript.net/other/Highly-Customizable-jQuery-Toast-Message-Plugin-Toastr.html) - JQuery Toast messages

  

## Notes:

- The app contains little to no JS (save for a few usability plugins), so it is extremely server-side, expect delays on endpoint communication

- The configuration provided is very simple, but direct JSON requests can be used on the Creation/Edition endpoints using the `<textarea>` elements on said pages 

  
## Deployment (for testing)

This deployment is for Debian 12/Debian based systems and uses Laravel Sail for a containerized Web & MySQL Servers

1. **Docker**

Install Docker Engine for use with Laravel Sail
```sh
apt update
apt install -y ca-certificates curl
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc
chmod a+r /etc/apt/keyrings/docker.asc

echo \
"deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/debian \
$(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
tee /etc/apt/sources.list.d/docker.list > /dev/null
apt update

apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```

- Add your user to the Docker group
```sh
usermod -aG docker <YOUR_USER>
```
2. **PHP & Composer**
- Install all required PHP packages and download Composer 
```sh
apt install -y php php-fpm php-curl php-gd php-dom php-xml php-zip zip unzip

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
```
- **!!! From this point onward, do not use root !!!**

3. **Repository**

- Download this repository
```sh
git clone https://github.com/Mali-prod/NetPilot.git
```

4. **Laravel Sail**
- Install with composer the laravel/sail package
```sh
composer require laravel/sail --dev
```

- Install Laravel Sail using artisan (select mysql)
```sh
php artisan sail:install 
```
- (Optional) Add ./vendor/bin/sail alias to .bashrc
```sh
echo "alias sail='./vendor/bin/sail'" >> $HOME/.bashrc
```
- Start the containers
```sh
sail up -d
```
- Make the DB migrations and data seeding
```sh
sail artisan migrate
sail artisan db:seed
```
5. Possible permission errors
- If there is any sort of permission problem, try running the chown command using root (alter the variables below to your user and folder)
```sh
chown -R <YOUR_USER> <NET_PILOT_FOLDER>
```
Everything should up and running now. The credentials are as follows:

Admin default account: `admin@example.com`
- Contains one device, Default_Device (`admin:123456`) via `http://192.168.88.1`

User default account: `user@example.com`

All passwords are `password`

---

© 2025 Artemis Technologies Ug Ltd
