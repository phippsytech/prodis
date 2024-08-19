// config.js
import dotenv from 'dotenv';


// Specify the path to the .env file
// and load environment variables from .env file
dotenv.config();

// Export the environment variables
export const apiUrl = process.env.API_URL;

