# OTP-Less Payment System

See following link for more details.

Link: [https://enindu.com/blog/otp-less-payment-system](https://enindu.com/blog/otp-less-payment-system)

## SSL Keys And Certificates

If alredy included SSL keys and certificates didn't work, use `config.conf` file to generate SSL keys and certificate. You may need to generate keys and certificates for every component.

### Generate SSL Key

```
openssl genrsa -out key.pem 2048
```

### Generate SSL Certificate

```
openssl req -nodes -new -x509 -sha256 -days 365 -config config.conf -extensions 'req_ext' -key key.pem -out certificate.pem
```

## Components

### Client Component

- Base URL: http://localhost
- Port: 8080

### Authentication Component

- Base URL: https://localhost:5003
- API URL: https://localhost:5003/api
- Port: 5003

### Middleware Component

- Base URL: https://localhost:5001
- API URL: https://localhost:5001/api
- Port: 5001

### Core Component

- Base URL: https://localhost:5002
- API URL: https://localhost:5002/api
- Port: 5002
