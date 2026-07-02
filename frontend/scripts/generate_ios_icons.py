import os
from PIL import Image, ImageDraw

logo_path = '../src/static/images/logo.svg'
output_dir = '../src/static/images/apple-icons'

os.makedirs(output_dir, exist_ok=True)

pink_color = '#ff6b9d'
white_color = '#ffffff'

sizes = [
    (57, 57),
    (76, 76),
    (87, 87),
    (120, 120),
    (152, 152),
    (167, 167),
    (180, 180),
]

for size in sizes:
    img = Image.new('RGBA', size, pink_color)
    draw = ImageDraw.Draw(img)
    
    center_x = size[0] // 2
    center_y = size[1] // 2
    heart_size = min(size) * 0.5
    
    top_y = center_y - heart_size * 0.4
    bottom_y = center_y + heart_size * 0.6
    left_x = center_x - heart_size * 0.5
    right_x = center_x + heart_size * 0.5
    
    points = [
        (center_x, top_y),
        (right_x, center_y),
        (center_x, bottom_y),
        (left_x, center_y),
    ]
    
    draw.polygon(points, fill=white_color)
    
    circle_size = heart_size * 0.35
    draw.ellipse([
        (center_x - circle_size * 0.5, center_y - circle_size * 0.5),
        (center_x + circle_size * 0.5, center_y + circle_size * 0.5)
    ], fill=pink_color)
    
    filename = f'apple-touch-icon-{size[0]}x{size[1]}.png'
    img.save(os.path.join(output_dir, filename))
    print(f'Generated: {filename}')

print('All icons generated successfully!')