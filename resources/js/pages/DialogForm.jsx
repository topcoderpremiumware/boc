import React, {useEffect, useRef, useState} from 'react';
import {
  Box,
  Card,
  CircularProgress,
  IconButton,
  InputBase,
  Paper,
} from "@mui/material";
import SendIcon from '@mui/icons-material/Send';
import AttachFileIcon from '@mui/icons-material/AttachFile';
import {VisuallyHiddenInput} from "../components/VisuallyHiddenInput.jsx";

export function DialogForm() {
  const [userId, setUserId] = useState(null);
  const [message, setMessage] = useState(null);
  const [messages, setMessages] = useState([]);
  const fileInputRef = useRef(null);
  const [loading, setLoading] = useState(false);
  const [file, setFile] = useState(null)

  useEffect(() => {
    if(localStorage.getItem('userId')){
      setUserId(localStorage.getItem('userId'))
    }else{
      let randomId = Math.floor(Math.random()*10000)
      localStorage.setItem('userId',randomId)
      setUserId(randomId)
    }
  }, [])

  const sendMessage = () => {
    setLoading(true)
    if(message) setMessages(prev => ([message, ...prev]))
    let formData = new FormData()
    if(message) formData.append('text', message)
    if(file) formData.append('file', file)
    setMessage(null)
    setFile(null)
    axios.post(`${import.meta.env.VITE_API_URL}/api/messages/${userId}`, formData,{
      headers: {

      }
    }).then(response => {
      setLoading(false)
      setMessages(prev => ([response.data.answer, ...prev]))
    }).catch(error => {
      setLoading(false)
      console.log(error)
    })
  }

  return (<Card sx={{margin: "10px 0"}}>
    <Box height="calc(100vh - 20px)" display="flex" flexDirection="column">
      <Box flex="1" overflow="auto" display="flex" flexDirection="column-reverse" justifyContent="end" padding="10px" gap="10px">
        <>{messages.map((message,key) => <Paper
          elevation={1}
          sx={{padding: "5px 10px", overflowWrap: "break-word"}}
          key={key}>{message}</Paper>)}</>
      </Box>
      {loading ? <CircularProgress sx={{margin: "auto"}}/> : null}
      <Box display="flex" flexDirection="row" padding="10px" alignItems="end">
        <VisuallyHiddenInput ref={fileInputRef} type="file" accept=".txt" onChange={(e) => setFile(e.target.files[0])} />
        <IconButton type="button" sx={{ p: '10px' }} aria-label="send" onClick={() => fileInputRef.current.click()}>
          <AttachFileIcon />
        </IconButton>
        <InputBase
          sx={{ flex: 1, p: '10px' }}
          multiline={true}
          maxRows={9}
          placeholder="Write a message"
          inputProps={{ 'aria-label': 'write a message' }}
          value={message || ''}
          onChange={(e) => setMessage(e.target.value)}
        />
        <IconButton type="button" sx={{ p: '10px' }}
          aria-label="send" onClick={sendMessage}>
          <SendIcon />
        </IconButton>
      </Box>
    </Box>
  </Card>)
}
