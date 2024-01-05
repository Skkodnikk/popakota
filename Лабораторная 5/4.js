function checkCondition(words) {
    if (words.length <= 1) {
        return true;
    }
    for (let i = 1; i < words.length; i++) {
        const currentWord = words[i];
        const previousWord = words[i - 1];
    if (currentWord.charAt(0).toLowerCase() !== previousWord.charAt(previousWord.length - 1).toLowerCase()) {
        return false;
    	}
    }
    return true;
}