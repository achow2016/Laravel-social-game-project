module.exports = {
  testRegex: 'public/js/test/.*.spec.js$',
  moduleFileExtensions: [
    'js',
    'json',
    'vue'
  ],
  'transform': {
    '^.+\\.js$': '<rootDir>/node_modules/babel-jest',
    '.*\\.(vue)$': '<rootDir>/node_modules/vue-jest'
  },
}