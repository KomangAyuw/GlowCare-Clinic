const fs = require('fs');
const path = require('path');

const replacements = {
  // Primary (Teal -> Brown)
  '#458b8b': '#735a39',
  '458b8b': '735a39',
  '#326666': '#594323',
  '326666': '594323',
  '#244c4c': '#402a12',
  '#66a3a3': '#e0c097',
  'rgba(69, 139, 139': 'rgba(115, 90, 57',
  'rgba(69,139,139': 'rgba(115,90,57',
  
  // Dark text (Navy -> Charcoal)
  '#2c3e50': '#2D3436',
  '2c3e50': '2D3436',
  
  // Backgrounds & Borders (Light Gray/Teal -> Cream/Warm Gray)
  '#f0f5f5': '#F9F7F2',
  'f0f5f5': 'F9F7F2',
  '#f5fafa': '#fbf9f4',
  '#e6f0f0': '#f5f3ee',
  '#dce8e8': '#d1c4b8',
  '#7f8c8d': '#7a7571',
  '#596b6b': '#585552',
  
  // Gradients
  '#3a5c5c': '#58442b',
};

const files = [
  'asset/css/user.css',
  'asset/css/admin.css',
  'asset/css/dokter.css',
  'asset/css/detail.css',
  'pages/user/dashboarduser.php',
  'pages/admin/dashboard.php',
  'pages/dokter/dashboardDokter.php'
];

files.forEach(file => {
  const filePath = path.join(__dirname, file);
  if (fs.existsSync(filePath)) {
    let content = fs.readFileSync(filePath, 'utf8');
    let original = content;
    
    for (const [oldStr, newStr] of Object.entries(replacements)) {
      content = content.split(oldStr).join(newStr);
      // Handle uppercase hex codes
      content = content.split(oldStr.toUpperCase()).join(newStr);
    }
    
    if (content !== original) {
      fs.writeFileSync(filePath, content, 'utf8');
      console.log('Updated: ' + file);
    }
  } else {
    console.log('Not found: ' + file);
  }
});
