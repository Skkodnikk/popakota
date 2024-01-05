function findKthDigit(k) {
    let sequence = '';
    for (let i = 1; i <= 300; i++) {
        sequence += i;
    }
    const kthDigit = sequence[k - 1];
    return kthDigit;
}