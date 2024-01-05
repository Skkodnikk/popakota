function isArithmeticSequence(number) {
    const digits = Math.abs(Math.trunc(number)).toString();
    for (let i = 1; i < digits.length; i++) {
        if (parseInt(digits[i]) !== parseInt(digits[i - 1]) + 1) {
            return false;
        }
    }
    return true;
}
function filterArray(array) {
    return array.filter(element => !isArithmeticSequence(element));
}