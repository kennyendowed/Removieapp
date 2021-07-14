import matUI from "../commonImport/commonImport";


class Header extends matUI.Component {
  
  render() {
    const useStyles = matUI.makeStyles((theme) => ({
      '@global': {
        ul: {
          margin: 0,
          padding: 0,
          listStyle: 'none',
        },
      },
      appBar: {
        borderBottom: `1px solid ${theme.palette.divider}`,
      },
      toolbar: {
        flexWrap: 'wrap',
      },
      toolbarTitle: {
        flexGrow: 1,
      },
      link: {
        margin: theme.spacing(1, 1.5),
      }
    
    }));
  
    return (
      
      <matUI.AppBar
        position="static"
        color="default"
        elevation={0}
        className={classes.appBar}
      >
        <matUI.Toolbar className={classes.toolbar}>
          <matUI.Typography
            variant="h6"
            color="inherit"
            noWrap
            className={classes.toolbarTitle}
          >
            Company name
          </matUI.Typography>
          <nav>
            <matUI.Link
              variant="button"
              color="textPrimary"
              href="#"
              className={classes.link}
            >
              Features
            </matUI.Link>
            <matUI.Link
              variant="button"
              color="textPrimary"
              href="#"
              className={classes.link}
            >
              Enterprise
            </matUI.Link>
            <matUI.Link
              variant="button"
              color="textPrimary"
              href="#"
              className={classes.link}
            >
              Support
            </matUI.Link>
          </nav>
        </matUI.Toolbar>
      </matUI.AppBar>
    );
  }
}

export default Header;
