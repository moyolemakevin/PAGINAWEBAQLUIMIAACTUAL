const express = require('express');

function jsonValidator(req, res, next) {
  if (req.is('application/json')) {
    let body = '';
    req.on('data', chunk => {
      body += chunk;
    });
    req.on('end', () => {
      try {
        if (/<\/?[a-z][\s\S]*>/i.test(body)) {
          return res.status(400).json({ error: 'Invalid JSON: HTML detected' });
        }
        req.body = JSON.parse(body || '{}');
        next();
      } catch (err) {
        res.status(400).json({ error: 'Invalid JSON' });
      }
    });
  } else {
    next();
  }
}

module.exports = jsonValidator;
