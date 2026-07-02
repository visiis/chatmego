from PIL import Image, ImageDraw
import os

def create_heart_icon(width, height):
    img = Image.new('RGBA', (width, height), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)
    
    x_center = width // 2
    y_center = height // 2
    scale = min(width, height) / 160
    
    points = [
        (x_center, y_center - 45 * scale),
        (x_center + 80 * scale, y_center - 85 * scale),
        (x_center + 80 * scale, y_center - 40 * scale),
        (x_center + 120 * scale, y_center + 10 * scale),
        (x_center + 60 * scale, y_center + 60 * scale),
        (x_center, y_center + 85 * scale),
        (x_center - 60 * scale, y_center + 60 * scale),
        (x_center - 120 * scale, y_center + 10 * scale),
        (x_center - 80 * scale, y_center - 40 * scale),
        (x_center - 80 * scale, y_center - 85 * scale),
    ]
    
    draw.polygon(points, fill=(255, 107, 157, 255))
    
    return img

def generate_ios_icons():
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    output_dir = os.path.join(base_dir, 'src', 'static', 'images', 'apple-icons')
    os.makedirs(output_dir, exist_ok=True)
    
    icon_sizes = [
        (180, 180, 'apple-touch-icon-180x180.png'),
        (167, 167, 'apple-touch-icon-167x167.png'),
        (152, 152, 'apple-touch-icon-152x152.png'),
        (120, 120, 'apple-touch-icon-120x120.png'),
        (87, 87, 'apple-touch-icon-87x87.png'),
        (76, 76, 'apple-touch-icon-76x76.png'),
        (57, 57, 'apple-touch-icon-57x57.png'),
    ]
    
    print("Generating iOS icons...")
    
    for width, height, filename in icon_sizes:
        img = create_heart_icon(width, height)
        output_path = os.path.join(output_dir, filename)
        img.save(output_path, 'PNG')
        print(f"  Generated: {filename} ({width}x{height})")
    
    print("\nAll iOS icons generated successfully!")

if __name__ == '__main__':
    generate_ios_icons()