/*
 * Copyright (c) 2021. TIV.NET INC. All Rights Reserved.
 */

// console.log(process.env.npm_lifecycle_event);
// console.log(process.env.npm_package_name); // foo
// console.log(process.env.npm_package_version); // 1.2.5

const exe = process.argv[2];
const args = process.argv.slice(3);
// console.log(exe);
console.log(args);

args.forEach(function (item, i) {
	args[i] = item
		.replace("%npm_package_version%", process.env.npm_package_version)
		.replace("%npm_package_title%", process.env.npm_package_name)
		.replace("%npm_package_name%", process.env.npm_package_name)
	;
});


const exec = require('child_process').execFile;
exec(exe, args, function (err, data) {
	if (err) console.log(err);
	console.log(data.toString());
});
