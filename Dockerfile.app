# Use an official Node.js runtime as a parent image
FROM node:18.17.0

# Set environment variable for non-interactive mode
ENV DEBIAN_FRONTEND=noninteractive

ENV NODE_ENV=development


# Install vite
# RUN npm install -g vite

# Set the working directory
WORKDIR /app

# Copy package.json and package-lock.json
COPY ./app/package*.json ./
COPY shared /app/shared



# Install dependencies
RUN npm install --include=dev

# Copy the rest of the application code
COPY ./app /app

# Copy the app.conf file
# COPY app.conf /app/app.conf

# Expose the default port for Vite
EXPOSE 5173

# Command to run the application
CMD ["npm", "run", "dev"]
