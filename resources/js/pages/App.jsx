import React, { Suspense, useEffect } from 'react';
import {createRoot} from 'react-dom/client';
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { createTheme, ThemeProvider } from '@mui/material/styles';
import LoadingPage from "./LoadingPage.jsx";
import {Container} from "@mui/material";
import {DialogForm} from "./DialogForm.jsx";
import Shop from "./Shop.jsx";

function App() {

  useEffect(() => {

  }, [])

  window.theme = createTheme({
      palette: {
          primary: {main:'#EFC719'},
          secondary: {main:'#343143'},
      },
  });

  return (
    <BrowserRouter>
      <ThemeProvider theme={window.theme}>
        <Suspense fallback={<LoadingPage/>}>
          <Container maxWidth="sm">
            <Routes>
              <Route path='/' exact element={<Suspense fallback={<LoadingPage/>}>
                <DialogForm/>
              </Suspense>}/>
              <Route path='/shop' exact element={<Suspense fallback={<LoadingPage/>}>
                <Shop/>
              </Suspense>}/>
            </Routes>
          </Container>
        </Suspense>
      </ThemeProvider>
    </BrowserRouter>
  );
}

export default App;

if (document.getElementById('app')) {
  const root = createRoot(document.getElementById("app"));
  root.render(<App/>)
}

