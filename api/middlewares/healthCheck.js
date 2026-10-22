async function healthCheck(req, res, next) {
  try {
    await Promise.all([
      checkDatabase(),
      checkStorage(),
      checkQueue(),
    ]);
    next();
  } catch (err) {
    res.status(503).json({ error: 'Service Unavailable' });
  }
}

async function checkDatabase() {
  // Placeholder for real DB check
  return true;
}
async function checkStorage() {
  return true;
}
async function checkQueue() {
  return true;
}

module.exports = healthCheck;
