 module.exports = {
	// prefix all files
	multiple_files: {
		expand  : true,
		flatten : true,
		src     : 'assets/css/*.css',
		dest    : 'assets/css/'
	},

	sourcemap: {
		options: {
			map: true
		}
	},
};
