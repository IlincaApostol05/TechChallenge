# TechChallenge
The project aims to identify outliers in price data for various global stock exchanges by implementing two APIs. The first API retrieves 30 consecutive data points from each file, starting from a random timestamp. The second API identifies outliers based on data exceeding 2 standard deviations from the mean of the sampled points. Input parameters specify the number of files to sample for each exchange (1 or 2), and the output includes .csv files detailing outlier data and percentage deviations. Effective error handling is implemented to manage exceptions and ensure robust performance.

This is a coding challenge implemented in PHP using Docker and the Symfony framework, following the MVC pattern.

# Requirements
Before running this project, make sure you have the following installed:

PHPStorm,
Docker,
An API development tool like Postman or Insomnia

# Getting Started
1.Clone the repository to your local machine.

2.Navigate to the project directory.

3.Execute the following command to start the Docker containers:

    docker-compose up -d
APIs
After starting the server, you can access the following APIs:

1. Import API
Endpoint: POST localhost/api/import

Description: Upload 1 or 2 CSV files containing the correct format(Each file has 
:Stock-ID (string), Timestamp (dd-mm-yyyy), stock price value(float)).

Output: 30 random data points extracted from each file, stored in new CSV file/s named random(1,2).csv ,in the var folder.

2. Outliers API
Endpoint: GET localhost/api/outlier

Description: Retrieve outliers from the 30 sampled data points.

Criteria for Outliers: Any datapoint that is over 2 standard deviations beyond the mean.

Output: Outliers are printed in one or two CSV files,stored in the var folder.
