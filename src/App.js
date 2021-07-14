import  matUI from './components/commonImport/commonImport';
import Header from './components/layout/Header';
import './App.css';

function Copyright() {
  return (
    <matUI.Typography variant="body2" color="textSecondary" align="center">
      {'Copyright © '}
      <matUI.Link color="inherit" href="https://material-ui.com/">
        Your Website
      </matUI.Link>{' '}
      {new Date().getFullYear()}
      {'.'}
    </matUI.Typography>
  );
}
const useStyles = matUI.makeStyles((theme) => ({
  '@global': {
    ul: {
      margin: 0,
      padding: 0,
      listStyle: 'none',
    },
  },
 
  heroContent: {
    padding: theme.spacing(8, 0, 6),
  },
  cardHeader: {
    backgroundColor:
      theme.palette.type === 'light' ? theme.palette.grey[200] : theme.palette.grey[700],
  },
  cardPricing: {
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'baseline',
    marginBottom: theme.spacing(2),
  },
  footer: {
    borderTop: `1px solid ${theme.palette.divider}`,
    marginTop: theme.spacing(8),
    paddingTop: theme.spacing(3),
    paddingBottom: theme.spacing(3),
    [theme.breakpoints.up('sm')]: {
      paddingTop: theme.spacing(6),
      paddingBottom: theme.spacing(6),
    },
  },
}));

const footers = [
  {
    title: 'Company',
    description: ['Team', 'History', 'Contact us', 'Locations'],
  },
  {
    title: 'Features',
    description: ['Cool stuff', 'Random feature', 'Team feature', 'Developer stuff', 'Another one'],
  },
  {
    title: 'Resources',
    description: ['Resource', 'Resource name', 'Another resource', 'Final resource'],
  },
  {
    title: 'Legal',
    description: ['Privacy policy', 'Terms of use'],
  },
];


function App() {
  const classes = useStyles();
  return (
    <div className="App">
       <matUI.CssBaseline />
   <Header />
   
            {/* Hero unit */}
            <matUI.Container maxWidth="sm" component="main" className={classes.heroContent}>
        <matUI.Typography component="h1" variant="h2" align="center" color="textPrimary" gutterBottom>
          Pricing
        </matUI.Typography>
        <matUI.Typography variant="h5" align="center" color="textSecondary" component="p">
          Quickly build an effective pricing table for your potential customers with this layout.
          It&apos;s built with default Material-UI components with little customization.
        </matUI.Typography>
      </matUI.Container>
      {/* End hero unit */}
      <matUI.Container maxWidth="md" component="main">





        </matUI.Container>


        {/* Footer */}
        <matUI.Container maxWidth="md" component="footer" className={classes.footer}>
        <matUI.Grid container spacing={4} justifyContent="space-evenly">
          {footers.map((footer) => (
            <matUI.Grid item xs={6} sm={3} key={footer.title}>
              <matUI.Typography variant="h6" color="textPrimary" gutterBottom>
                {footer.title}
              </matUI.Typography>
              <ul>
                {footer.description.map((item) => (
                  <li key={item}>
                    <matUI.Link href="#" variant="subtitle1" color="textSecondary">
                      {item}
                    </matUI.Link>
                  </li>
                ))}
              </ul>
            </matUI.Grid>
          ))}
        </matUI.Grid>
        <matUI.Box mt={5}>
          <Copyright />
        </matUI.Box>
      </matUI.Container>
      {/* End footer */}
    </div>
  );
}

export default App;
