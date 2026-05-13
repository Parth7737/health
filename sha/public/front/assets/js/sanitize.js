function sanitize(input, type = '', maxLength = null) {
    let value = input.value;

    if (type === 'm') {
        value = value.replace(/[^a-zA-Z0-9 @.-]/g, '');
        let at = 0, dt = 0, dc = 0;
        value = value.split('').filter((c) => {
            if (c === '@') { at++; return at <= 1; }
            if (c === '.') { dt++; return dt <= 1; }
            if (c === '-') { dc++; return dc <= 1; }
            return true;
        }).join('');
    } else if (type === 't') {
        value = value.replace(/[^a-zA-Z ]/g, '');
    } else if (type === 'n') {
        value = value.replace(/[^0-9]/g, '');
    } else if (type === 'b') {
        value = value.replace(/[^a-zA-Z0-9 .]/g, '');
    } else if (type === 'email') {
        value = value.replace(/[^a-zA-Z0-9@._-]/g, '');
    } else if (type === 'd') {
        value = value.replace(/[^0-9\-\/.]/g, ''); // Allow only numbers, -, /, or .
        if (!isValidDate(value)) {
            value = ''; // Clear invalid date
        }
    }

    // Apply max length validation
    if (maxLength !== null && value.length > maxLength) {
        value = value.substring(0, maxLength);
    }

    if (input.value !== value) {
        input.value = value;
    }       
}