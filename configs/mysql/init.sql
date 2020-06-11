CREATE TABLE IF NOT EXISTS exchange_rates (
    base_currency CHAR(3) NOT NULL,
    currency CHAR(3) NOT NULL,
    rate_date DATE NOT NULL,
    rate DOUBLE NOT NULL,
    PRIMARY KEY (base_currency, currency, rate_date)
)
