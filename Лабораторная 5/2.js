function findAndSum(array) {
    let sum = 0;
    let found = false;
    for (let i = 0; i < array.length; i++) {
        if (Math.cos(array[i]) > 0) {
            found = true;
        }
        if (found) {
            sum += array[i];
        }
    }
    return sum;
}