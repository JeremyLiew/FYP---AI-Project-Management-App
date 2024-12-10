export function formatDate(dateString, format = 'DD/MM/YYYY') {
	const date = new Date(dateString);
	const day = String(date.getDate()).padStart(2, '0');
	const month = String(date.getMonth() + 1).padStart(2, '0');
	const year = date.getFullYear();

	// Apply the requested format
	if (format === 'DD/MM/YYYY') {
		return `${day}/${month}/${year}`;
	} else if (format === 'MM/DD/YYYY') {
		return `${month}/${day}/${year}`;
	} else {
		// Default format: YYYY-MM-DD
		return `${year}-${month}-${day}`;
	}
}
