

export function time(timeString) {
    const [hours, minutes] = timeString.split(':').map(Number);
    const date = new Date();
    date.setHours(hours, minutes, 0, 0);
    console.log(date)
    return date;
}

export function createDateTime(dateString, timeString) {
    console.log(new Date(`${dateString}T${timeString}:00`))
    return new Date(`${dateString}T${timeString}:00`);
}
