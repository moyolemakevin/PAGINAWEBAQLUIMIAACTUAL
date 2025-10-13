const express = require('express');
const path = require('path');
const multer = require('multer');
const sharp = require('sharp');
const jsonValidator = require('./middlewares/jsonValidator');
const healthCheck = require('./middlewares/healthCheck');

const app = express();
const uploadDir = path.join(__dirname, '../public/uploads');
const storage = multer.memoryStorage();
const upload = multer({
  storage,
  fileFilter: (req, file, cb) => {
    const allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/tiff'];
    if (!allowed.includes(file.mimetype)) {
      return cb(new Error('Unsupported format'));
    }
    cb(null, true);
  }
});

app.use(jsonValidator);
app.use(healthCheck);
app.post('/api/products/:id/images', upload.array('images'), async (req, res) => {
  try {
    if (!req.files || req.files.length === 0) {
      return res.status(400).json({ error: 'No images uploaded' });
    }
    const results = [];
    for (const file of req.files) {
      const filename = `prod_${req.params.id}_${Date.now()}_${Math.random().toString(36).slice(2)}.jpg`;
      const filepath = path.join(uploadDir, filename);
      await sharp(file.buffer)
        .resize({ width: 2000, height: 2000, fit: 'inside' })
        .jpeg({ quality: 80 })
        .toFile(filepath);
      results.push(`/uploads/${filename}`);
    }
    res.json({ success: true, files: results });
  } catch (err) {
    if (err.message === 'Unsupported format') {
      res.status(400).json({ error: err.message });
    } else {
      res.status(500).json({ error: 'Error interno al procesar la imagen' });
    }
  }
});

module.exports = app;

if (require.main === module) {
  const port = process.env.PORT || 3000;
  app.listen(port, () => console.log(`Server running on port ${port}`));
}
