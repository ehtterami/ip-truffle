
# IP Truffle
IPv4 translator script for converting IPv4 to Binary and reverse.
## Features

- Translates IPv4 to Binary
- Translates Binary to IPv4
- Translates IPv4 range
- Calculate custom subnet mask
- Calculate Network ID


## Prerequest
- PHP 8.3.15 or higher
- Composer (Package management)

## Deployment

To deploy and use this script follow these steps:

#### 1. Clone the repository on your system
```bash
git clone https://github.com/ehtterami/ip-truffle.git
```

#### 2. Go to scripts folder 
```bash
cd ip-truffle
```

#### 3. Run the script with PHP
```bash
php main.php translate 192.168.1.1
```

Remember you should at least have PHP version 8.3.15 on your system. 
## Usage/Examples

#### 1. Simple convert IPv4 to Binary
```bash
php main.php translate -i 192.168.1.1
```

#### 2. Translate IPv4 to Binary with CIDR
```bash
php main.php translate -i 192.168.1.1/24
```

#### 3. Translate Binary to IPv4
```bash
php main.php translate -i 11000000.10101000.00000001.00000001
```

#### 4. Calculate custom subnet mask based on hosts
```bash
php main.php calculate:subnet -b 192.168.1.1 -c 200
```

#### 3. Calculate Network ID
```bash
php main.php calculate:net-id -i 192.168.1.10 -m 255.255.255.0
```
## Feedback

If you have any feedback, please reach out to us at ehtterami@gmail.com

