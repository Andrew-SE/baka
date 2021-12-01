
module.exports = {
  mode: 'jit',
  purge: [
      './views/*.php',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      colors: {
        white: {
          tr: '#ffffff00',
          DEFAULT: '#fff'
        },
        blue:{
          DEFAULT: '#98c1d9',
          light: '#98c1d955',
          bcg: '#293241',
          bgop: '#293241dd',
        },
        gray: {
          300: '#BEBEBE',
          DEFATULT: '#808080',
          500 : '#696969',
          footer: '#2b2b2b'
        },
        orange: {
          DEFAULT: '#ee6c4d',

        },
        green: {
          DEFAULT: '#2ECC71'
        },
      },

      fontSize: {
        '2xs':'.65rem',
        '7xl': '5rem',
      },
      minHeight: {
        '1/4': '25%',
        '1/2': '50%',
        '3/4': '75%',

        '5vh' : '5vh',
        '1/4vh': '25vh',
        '1/2vh': '50vh',
        '3/4vh': '75vh',
        '95vh':'95vh',
      },
      minWidth: {
        '1/4': '25%',
        '30': '30%',
        '35': '35%',
        '1/2': '50%',
        '3/4': '75%',

        '5vh' : '5vh',
        '1/4vh': '25vh',
        '1/2vh': '50vh',
        '3/4vh': '75vh',
        '95vh':'95vh',
      },
      maxWidth: {
        'sm' : '70px',
        'smr' : '16rem',
        '1/4': '25%',
        '1/2': '50%',
        '3/4': '75%',
        '5vh' : '5vw',
        '1/4vh': '25vw',
        '1/2vh': '50vw',
        '3/4vh': '75vw',
        '95vh':'95vw',
      },
    },

  },
  variants: {
    extend: {
      maxHeight: ['focus'],
      minWidth: ['focus', 'hover','active'],

      margin: ['focus'],
      padding: ['focus'],
    }
  },
  plugins: [
  ],
}
