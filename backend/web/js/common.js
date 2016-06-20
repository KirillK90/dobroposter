function translitUrl(word){
    // Символ, на который будут заменяться все спецсимволы
    var space = '-';
// Берем значение из нужного поля и переводим в нижний регистр
    var text = word.toLowerCase();

// Массив для транслитерации
    var trans = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'e', 'ж': 'zh',
        'з': 'z', 'и': 'i', 'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
        'о': 'o', 'п': 'p', 'р': 'r','с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h',
        'ц': 'c', 'ч': 'ch', 'ш': 'sh', 'щ': 'sh','ъ': space, 'ы': 'y', 'ь': space, 'э': 'e', 'ю': 'yu', 'я': 'ya',
        ' ': space, '`': space, '~': space, '!': space, '@': space,
        '#': space, '$': space, '%': space, '^': space, '&': space, '*': space,
        '(': space, ')': space,'-': space, '\=': space, '+': space, '[': space,
        ']': space, '\\': space, '|': space, '/': space,'.': space, ',': space,
        '\'': space, '"': space, ';': space, '?': space, '<': space, '>': space, '№':space
    };
    return text.split('').map(function (char) {
        return trans[char] || char;
    }).join("");
}

var text = $("textarea[maxlength]");
text.parent().append('<div class="counter_msg"><div>');

text.on('focus keyup', '', function (e) {

    var $this = $(this);
    var msgSpan = $this.parent().find('.counter_msg');
    var ml = parseInt($this.attr('maxlength'), 10);
    var length = this.value.length;
    var msg = ml - length + ' символов из ' + ml + ' осталось';
    console.log(length);
    msgSpan.html(msg);
});