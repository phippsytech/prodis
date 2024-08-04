# Step 1: Build the Svelte app
FROM node:18.17.0 AS build

# Set environment variable for non-interactive mode
ENV DEBIAN_FRONTEND=noninteractive

# Set the working directory
WORKDIR /app

# Copy package.json and package-lock.json
COPY ./app/package*.json ./
COPY ../shared /app/shared

# Install dependencies
RUN npm install --include=dev

# Copy the rest of the application code
COPY ./app /app

# Run the build command
RUN npm run build

# Step 2: Serve the built files with a simple web server
FROM nginx:alpine

# Copy the built files from the previous stage
COPY --from=build /app/dist /usr/share/nginx/html

# Expose the default port for nginx
EXPOSE 80

# Start nginx
CMD ["nginx", "-g", "daemon off;"]
