export function formatXeroDate(str_date){
        
    const paymentDate = str_date;
    const timestamp = parseInt(paymentDate.substring(6, 19));
    const date = new Date(timestamp);

    return date.toLocaleDateString("en-UK", {
        day: "numeric",
        month: "short", // short, numeric
        year: "numeric"
    });

}