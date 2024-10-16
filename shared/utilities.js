

// check if any elements in arr1 are also in arr2
export function haveCommonElements(arr1, arr2) {
    // Check if both arrays are arrays
    if (Array.isArray(arr1) === false || Array.isArray(arr2) === false) return false

    // Use the some() method to check if any elements in arr1 are also in arr2
    return arr1.some(function (element) {
        return arr2.includes(element);
    });
}


export function formatCurrency(number) {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(number);
}

export function convertMinutesToHoursAndMinutes(minutes) {
    let hours = Math.floor(minutes / 60);
    let remainingMinutes = Math.floor(minutes % 60);
    let result = "";

    if (hours > 0) {
        result += `${hours}<span class="text-xs font-lighter opacity-65">hr${hours > 1 ? 's' : ''}</span>`;
    }
    if (remainingMinutes > 0) {
        if (result.length > 0) {
            result += " ";
        }
        result += `${remainingMinutes}<span class="text-xs font-lightest opacity-70">min${remainingMinutes > 1 ? 's' : ''}</span>`;
    }

    return result;
}

export function convertDecimalHoursToHoursAndMinutes(decimalHours) {
    const minutes = decimalHours * 60;
    return convertMinutesToHoursAndMinutes(minutes);
}

export function getLocalDate() {
    let date = new Date();
    let year = date.getFullYear();
    let month = ("0" + (date.getMonth() + 1)).slice(-2); // Months are 0 based, so we add 1
    let day = ("0" + date.getDate()).slice(-2); // Pad with leading 0 if needed
    let formattedDate = `${year}-${month}-${day}`;
    return formattedDate;
}


export function convertMicrosoftDate(microsoftDate) {
    let date = new Date(parseInt(microsoftDate.substr(6)));
    return date.toISOString().slice(0, 10);
}

export function convertDateToUnixTimestamp(dateString) {

    if (!dateString) return null;

    const parts = dateString.split('-');
    const year = parts[0];
    const month = parts[1];
    const day = parts[2];

    const date = new Date(
        parseInt(year, 10),
        parseInt(month, 10) - 1,
        parseInt(day, 10)
    );
    return Math.floor(date.getTime() / 1000);
    // return parseInt(Math.floor(date.getTime() / 1000));
}


export function formatDate(str_date, options = {
    day: "numeric",
    month: "short", // short, numeric
    year: "numeric"
}) {

    if (options.year == null) delete (options.year);

    if (!str_date) return null;
    let parts = str_date.split('-');
    let year = parts[0];
    let month = parts[1];
    let day = parts[2];
    let date = new Date(
        parseInt(year, 10),
        parseInt(month, 10) - 1,
        parseInt(day, 10)
    );
    return date.toLocaleDateString("en-UK", options);
}

export function formatDateTime(
    str_date,
    options = {
        day: "numeric",
        month: "short", // short, numeric
        year: "numeric",
        hour: "numeric",
        minute: "2-digit",
        hour12: true // Use 12-hour format if true
    },
) {
    if (!options.year) delete options.year;

    if (!str_date) return null;

    let [datePart, timePart] = str_date.split(" ");
    let [year, month, day] = datePart.split("-");
    let [hour, minute, second] = timePart.split(":");

    // Parse the date as UTC
    let date = new Date(Date.UTC(
        parseInt(year, 10),
        parseInt(month, 10) - 1,
        parseInt(day, 10),
        parseInt(hour, 10),
        parseInt(minute, 10),
        parseInt(second, 10)
    ));

    // Format only the date part
    let formattedDate = date.toLocaleDateString("en-UK", {
        day: options.day,
        month: options.month,
        year: options.year,
    });

    // Format only the time part
    let formattedTime = date.toLocaleTimeString("en-UK", {
        hour: options.hour,
        minute: options.minute,
        hour12: options.hour12,
    });

    // Combine date and time with "at" in between
    return `${formattedDate} at ${formattedTime}`;
}

export function getDaysUntilDate(dateString) {
    const today = new Date();
    const inputDate = new Date(dateString);

    // Set the time portion of both dates to midnight
    today.setHours(0, 0, 0, 0);
    inputDate.setHours(0, 0, 0, 0);

    // Calculate the time difference between today and input date in milliseconds
    const timeDifference = inputDate.getTime() - today.getTime();

    // Calculate the number of days
    const daysDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));

    if (daysDifference === 0) {
        return "today";
    } else if (daysDifference === 1) {
        return "tomorrow";
    } else if (daysDifference === -1) {
        return "yesterday";
    } else if (daysDifference > 1) {
        return `in ${daysDifference} days`;
    } else {
        return `${Math.abs(daysDifference)} days ago`;
    }
}


export function getElapsedTime(start_time, end_time) {
    const [startHour, startMinute] = start_time.split(':');
    const [endHour, endMinute] = end_time.split(':');

    const startTime = new Date();
    startTime.setHours(startHour, startMinute, 0, 0);

    const endTime = new Date();
    endTime.setHours(endHour, endMinute, 0, 0);

    const elapsedMilliseconds = endTime - startTime;
    const elapsedSeconds = elapsedMilliseconds / 1000;
    const elapsedMinutes = elapsedSeconds / 60;

    return elapsedMinutes;
}



export function getDatePlus7Days(str_date) {
    let parts = str_date.split('-');
    let year = parts[0];
    let month = parts[1];
    let day = parts[2];
    let date = new Date(
        parseInt(year, 10),
        parseInt(month, 10) - 1,
        parseInt(day, 10)
    );
    date.setDate(date.getDate() + 7);
    return date.toISOString().slice(0, 10);
    // return date.toLocaleString().slice(0, 10).split('/').reverse().join('-');

}

export function getDatePlusDays(str_date, days) {
    let parts = str_date.split('-');
    let year = parts[0];
    let month = parts[1];
    let day = parts[2];
    let date = new Date(
        parseInt(year, 10),
        parseInt(month, 10) - 1,
        parseInt(day, 10)
    );
    date.setDate(date.getDate() + days);
    return date.toISOString().slice(0, 10);
    // return date.toLocaleString().slice(0, 10).split('/').reverse().join('-');    
}



export function getMonday() {
    let d = new Date();
    let day = d.getDay(),
        diff = d.getDate() - day + (day == 0 ? -6 : 1); // adjust when day is Sunday
    d.setDate(diff);

    // Format date as YYYY-MM-DD
    let year = d.getFullYear();
    let month = ("0" + (d.getMonth() + 1)).slice(-2); // Months are 0 based, so add 1 and pad with 0 if necessary
    day = ("0" + d.getDate()).slice(-2); // Pad with 0 if necessary

    return `${year}-${month}-${day}`;
}



export function convertToHoursAndMinutes(decimalHours) {
    const minutes = decimalHours * 60;
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = Math.floor(minutes % 60);
    let result = '';
    if (hours > 0) result = `${hours}hrs`;
    result += remainingMinutes > 0 ? ` ${remainingMinutes}mins` : '';
    return result;
}

export function convertFieldsToBoolean(data, fieldsToConvert) {


    return data.map(item => {
        fieldsToConvert.forEach(field => {
            if (item.hasOwnProperty(field)) {
                item[field] = item[field] === "1" || item[field] === 1; // Handle both string and numeric representations
            }
        });
        return item;
    });
}


// Function to calculate and format the time difference
export function timeAgoOLD(dateString) {
    const utcDate = new Date(dateString); // Convert the date string to a UTC Date object

    // Get the browser's timezone offset in minutes
    const timezoneOffset = new Date().getTimezoneOffset() * 60000; // Convert minutes to milliseconds

    // Adjust the UTC date by the timezone offset
    const localDate = new Date(utcDate.getTime() - timezoneOffset);

    const now = new Date(); // Get the current local date and time
    const diffInSeconds = Math.floor((now - localDate) / 1000); // Calculate the difference in seconds

    // Define time intervals in seconds
    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60,
        second: 1
    };

    // Loop through intervals to find the appropriate time difference
    for (const [unit, value] of Object.entries(intervals)) {
        const diff = Math.floor(diffInSeconds / value);
        if (diff > 1) {
            return `${diff} ${unit}s ago`; // Plural form
        } else if (diff === 1) {
            return `${diff} ${unit} ago`; // Singular form
        }
    }

    return 'just now'; // If the date is very recent
}

// Function to calculate and format the time difference
export function timeAgo(dateString) {
    const utcDate = new Date(dateString); // Convert the date string to a UTC Date object

    // Get the browser's timezone offset in minutes
    const timezoneOffset = new Date().getTimezoneOffset() * 60000; // Convert minutes to milliseconds

    // Adjust the UTC date by the timezone offset
    const localDate = new Date(utcDate.getTime() - timezoneOffset);

    const now = new Date(); // Get the current local date and time

    // Check if the provided date is the same as the current date
    if (localDate.toDateString() === now.toDateString()) {
        return 'today';
    }

    // Calculate the difference in seconds considering only the time portion of the date
    let diffInSeconds = Math.floor((now - localDate) / 1000); // Calculate the difference in seconds

    let timeSuffix = ''
    let timePrefix = ''; // Default to past dates
    if (diffInSeconds < 0) {
        diffInSeconds = -diffInSeconds; // Make the difference positive
        timePrefix = 'in '; // Change the prefix to indicate a future date
    } else {
        timeSuffix = ' ago';
    }

    // Define time intervals in seconds
    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60,
        second: 1
    };

    // Loop through intervals to find the appropriate time difference
    for (const [unit, value] of Object.entries(intervals)) {
        const diff = Math.floor(diffInSeconds / value);
        if (diff > 1) {
            return `${timePrefix}${diff} ${unit}s${timeSuffix}`; // Plural form
        } else if (diff === 1) {
            return `${timePrefix}${diff} ${unit}${timeSuffix}`; // Singular form
        }
    }

    return 'just now'; // If the date is very recent
}

export function convertToLocalDate(utcDate) {
    if (utcDate) {
        let date = new Date(utcDate + 'Z'); // append 'Z' to indicate UTC time
        return new Intl.DateTimeFormat('en-AU', { dateStyle: 'medium' }).format(date);
    }
}


export function decimalRounder(value) {
    return Math.round((value + Number.EPSILON) * 100) / 100;
}

// Function to extract query parameters from the hash
export function getQueryParams() {
    const hash = window.location.hash;
    const queryString = hash.split('?')[1]; // Get the part after ?
    const params = new URLSearchParams(queryString);
    const result = {};
    params.forEach((value, key) => {
        result[key] = value;
    });
    return result;
}

// format snake case to pretty name
export function formatPrettyName(str) {
    return str?.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}

export function getDateOnlyTimestamp(dateString) {
    const date = new Date(dateString);
    return new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime();
}