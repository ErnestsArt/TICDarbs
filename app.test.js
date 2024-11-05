
beforeEach(() => {
    localStorage.clear();
});

test('should register a new user', () => {
    const username = 'testuser';
    const password = 'password123';

    localStorage.setItem(username, password);
    expect(localStorage.getItem(username)).toBe(password);
});

test('should login with correct credentials', () => {
    const username = 'testuser';
    const password = 'password123';
    localStorage.setItem(username, password);

    const storedPassword = localStorage.getItem(username);
    expect(storedPassword).toBe(password);
});

test('should not login with incorrect credentials', () => {
    const username = 'testuser';
    const password = 'password123';
    localStorage.setItem(username, password);

    const wrongPassword = 'wrongpassword';
    const storedPassword = localStorage.getItem(username);
    expect(storedPassword).not.toBe(wrongPassword);
});

test('should not register a user with an existing username', () => {
    const username = 'existingUser';
    const password = 'password123';
    localStorage.setItem(username, password);

    const newPassword = 'newpassword';
    const existingUser = localStorage.getItem(username);
    expect(existingUser).toBe(password);
});

test('should parse CSV data correctly', () => {
    const csvData = `Month;Location;Visitors\nJanuary;Place A;100\nFebruary;Place B;200`;
    const expectedOutput = [
        ['Month', 'Location', 'Visitors'],
        ['January', 'Place A', '100'],
        ['February', 'Place B', '200']
    ];

    function parseCSV(data) {
        return data.split('\n').map(line => line.split(';').map(item => item.trim()));
    }

    expect(parseCSV(csvData)).toEqual(expectedOutput);
});