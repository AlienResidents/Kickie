The following words cover installing Kickie on Ubuntu 12.04.1

You'll need the following software installed:

* bind9
* isc-dhcp-server
* atftpd
* php5-mysql
* mysql-server
* apache2
* libapache2-mod-php5
* 

You should set the following variables in your bourne compatible shell of
choice

* BASE_DIR
* TFTP_DIR
* MEDIA_DIR
* HTML_DIR


Example

    BASEDIR=/app/kickstart
    BASEDIR=$BASE_DIR/pxe_boot
    MEDIA_DIR=$BASE_DIR/media
    HTML_DIR="$BASE_DIR/html
    DHCP_SUBNET=""


Install the required packages

    apt-get install bind9 isc-dhcp-server atftpd php5-mysql mysql-server \
    apache2 libapache2-mod-php5

 
Then configure the atftp server for use in xinetd using the variables you've set

    cat > /etc/xinetd.d/atftpd << EOF
    service tftp
    {
       protocol = udp
       port = 69
       socket_type = dgram
       wait = yes
       user = nobody
       server = /usr/sbin/atftpd
       server_args = /app/kickstart/pxe_boot -s
       disable = no
    }
    EOF


Restart xinetd

    stop xinetd
    start xinetd


Configure apache accordingly from your variables

    Alias /kickstart /app/kickstart/html
    Alias /ubuntu /app/kickstart/media
    <Location /ubuntu>
      Options Indexes
      Order allow,deny
      allow from all
    </Location>


Configure DHCP as needed

    cat >> /etc/dhcp/dhcpd.conf << EOF
      subnet 10.227.192.32 netmask 255.255.255.224 {
        range 10.227.192.36 10.227.192.62;
        option domain-name-servers 10.227.192.34;
        option bootfile-name "pxelinux.0";
        option domain-name "localdomain";
        option routers 10.227.192.34;
        default-lease-time 1800;
        max-lease-time 3600;
        next-server 10.227.192.34;
      }
    EOF



















