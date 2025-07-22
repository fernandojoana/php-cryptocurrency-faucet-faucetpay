# PHP Cryptocurrency Faucet with FaucetPay API

A complete PHP-based cryptocurrency faucet application that allows users to claim small amounts of cryptocurrency using the FaucetPay API for payments.

## Features

- **PHP-based backend** with MySQL database
- **Bootstrap CSS** for responsive design
- **FaucetPay API integration** for secure payments
- **Rate limiting** and anti-bot protection
- **Real-time balance checking**
- **Responsive design** for all devices

## Quick Setup

1. **Database Setup**
   ```sql
   CREATE DATABASE faucet_db;
   USE faucet_db;
   ```

2. **Configuration**
   - Edit `config.php` with your FaucetPay API key
   - Update database credentials

3. **Installation**
   - Upload all files to your web server
   - Access `index.php` to start using the faucet

## Configuration

### Required Settings in `config.php`:
- `FAUCETPAY_API_KEY`: Your FaucetPay API key
- `CURRENCY`: Your preferred cryptocurrency (BTC, LTC, DOGE, etc.)
- `CLAIM_AMOUNT`: Amount to pay per claim
- `CLAIM_INTERVAL`: Time between claims (in seconds)

## Usage

1. **Claiming Cryptocurrency**
   - Visit the faucet page
   - Enter your wallet address
   - Solve the CAPTCHA
   - Click "Reivindicar" to receive your payment

2. **Admin Features**
   - Monitor faucet balance
   - View claim history
   - Manage rate limiting

## Security Features

- IP-based rate limiting
- CAPTCHA verification
- Address validation
- Anti-bot protection
- Request logging

## API Endpoints

- `POST /api/claim` - Handle faucet claims
- `GET /api/balance` - Check faucet balance

## Support

For issues or questions, please refer to the documentation or contact support.
