var fs = require('fs')
fs.readFile('aside.blade.php', 'utf8', function (err, data) {
    if (err) {
        return console.log(err);
    }

    const result = data.replace(/string to be replaced/g, 'replacement');

    const resData = data.matchAll(/\{\{__\(\'(.*?)\'\)\}\}/gi);

    for (const iterator of resData) {
        console.log('"' + iterator[1] + '":"",');
    }
    console.log(resData);
    // ([а-я]+\s?[а-я]+)
    //   fs.writeFile(someFile, result, 'utf8', function (err) {
    //      if (err) return console.log(err);
    //   });
});