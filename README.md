# evDash_serverapi

Rest api for [evDash](https://github.com/nickn17/evDash)

# Notes

The library that evDash uses does not support HTTPS, therefore a server hosted with SSL will not be accessible and report reports in the serial console. Using HTTP only will allow API updates.

# Installation

1) Place files in a php enabled web server
2) Create MySQL/MariaDB Database
3) Import db.sql file into new database
4) Update config.php document in both GUI and API directories
*Note that the API document differs, including an ABRP Token*
5) Create API key (See below)


# Usage

## Registering new ApiKey
```bash
curl -X GET -H "Content-Type: application/json" http://api.example.com/\?register
```
#### Response
```json
{
  "ret": "ok",
  "apikey": "abcdef123456"
}
```

## Getting data from api

### Last record
```bash
curl -d '{"apikey":"abcdef123456"}' -H "Content-Type: application/json" -X GET https://api.example.com
```
#### Response
```json
{
  "values": {
    "timestamp": "2020-12-02 00:41:23",
    "carType": null,
    "socPerc": -1,
    "sohPerc": 562.7,
    "batPowerKw": -0.76747,
    "batPowerAmp": -0.7191,
    "batVoltage": 4.06,
    "auxVoltage": 27,
    "batMinC": 30,
    "batMaxC": -1,
    "batInletC": 0,
    "batFanStatus": 0,
    "cumulativeEnergyChargedKWh": 562.7,
    "cumulativeEnergyDischargedKWh": 507.5
  }
}
```

### Records by date
```bash
curl -d '{"apikey":"abcdef123456", "timestampFrom":"2020-12-05 00:20:00", "timestampTo":"2020-12-05 00:27:00"}' -H "Content-Type: application/json" -X GET https://api.example.com
```
#### Response
```json
{
  "values": [
    {
      "timestamp": "2020-12-05 00:20:20",
      "carType": 2,
      "socPerc": 41,
      "sohPerc": 100,
      "batPowerKw": 2.12646,
      "batPowerAmp": 6.1,
      "batVoltage": 348.6,
      "auxVoltage": 14.4,
      "batMinC": 23,
      "batMaxC": 24,
      "batInletC": 25,
      "batFanStatus": 0,
      "cumulativeEnergyChargedKWh": 3049.1,
      "cumulativeEnergyDischargedKWh": 2983
    },
    {
      "timestamp": "2020-12-05 00:21:21",
      "carType": 2,
      "socPerc": 41,
      "sohPerc": 100,
      "batPowerKw": 2.12646,
      "batPowerAmp": 6.1,
      "batVoltage": 348.6,
      "auxVoltage": 14.4,
      "batMinC": 23,
      "batMaxC": 24,
      "batInletC": 25,
      "batFanStatus": 0,
      "cumulativeEnergyChargedKWh": 3049.1,
      "cumulativeEnergyDischargedKWh": 2983
    },
    {
      "timestamp": "2020-12-05 00:22:22",
      "carType": 2,
      "socPerc": 41,
      "sohPerc": 100,
      "batPowerKw": 2.12646,
      "batPowerAmp": 6.1,
      "batVoltage": 348.6,
      "auxVoltage": 14.4,
      "batMinC": 23,
      "batMaxC": 24,
      "batInletC": 25,
      "batFanStatus": 0,
      "cumulativeEnergyChargedKWh": 3049.1,
      "cumulativeEnergyDischargedKWh": 2983
    },
    {
      "timestamp": "2020-12-05 00:23:24",
      "carType": 2,
      "socPerc": 41,
      "sohPerc": 100,
      "batPowerKw": 2.12646,
      "batPowerAmp": 6.1,
      "batVoltage": 348.6,
      "auxVoltage": 14.4,
      "batMinC": 23,
      "batMaxC": 24,
      "batInletC": 25,
      "batFanStatus": 0,
      "cumulativeEnergyChargedKWh": 3049.1,
      "cumulativeEnergyDischargedKWh": 2983
    },
    {
      "timestamp": "2020-12-05 00:24:25",
      "carType": 2,
      "socPerc": 41,
      "sohPerc": 100,
      "batPowerKw": 2.12646,
      "batPowerAmp": 6.1,
      "batVoltage": 348.6,
      "auxVoltage": 14.4,
      "batMinC": 23,
      "batMaxC": 24,
      "batInletC": 25,
      "batFanStatus": 0,
      "cumulativeEnergyChargedKWh": 3049.1,
      "cumulativeEnergyDischargedKWh": 2983
    },
    {
      "timestamp": "2020-12-05 00:25:25",
      "carType": 2,
      "socPerc": 41,
      "sohPerc": 100,
      "batPowerKw": 2.12646,
      "batPowerAmp": 6.1,
      "batVoltage": 348.6,
      "auxVoltage": 14.4,
      "batMinC": 23,
      "batMaxC": 24,
      "batInletC": 25,
      "batFanStatus": 0,
      "cumulativeEnergyChargedKWh": 3049.1,
      "cumulativeEnergyDischargedKWh": 2983
    },
    {
      "timestamp": "2020-12-05 00:26:27",
      "carType": 2,
      "socPerc": 41,
      "sohPerc": 100,
      "batPowerKw": 2.12646,
      "batPowerAmp": 6.1,
      "batVoltage": 348.6,
      "auxVoltage": 14.4,
      "batMinC": 23,
      "batMaxC": 24,
      "batInletC": 25,
      "batFanStatus": 0,
      "cumulativeEnergyChargedKWh": 3049.1,
      "cumulativeEnergyDischargedKWh": 2983
    }
  ]
}
```

