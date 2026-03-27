$code = @"
using System;
using System.Drawing;
using System.Drawing.Imaging;

public class ImageProcessor {
    public static void ProcessWhiteOnWhite(string inPath, string outPath) {
        using(Bitmap img = new Bitmap(inPath)) {
            for(int y=0; y<img.Height; y++) {
                for(int x=0; x<img.Width; x++) {
                    Color p = img.GetPixel(x,y);
                    
                    // The text is pure white, the background is shades of white/grey.
                    // We measure distance from pure white (255, 255, 255).
                    int diff = (255 - p.R) + (255 - p.G) + (255 - p.B);
                    
                    int alpha = 0;
                    if (diff < 10) { 
                        // Extremely close to pure white -> solid text
                        alpha = 255;
                    } else if (diff < 80) { 
                        // Shades of white/grey -> blend to transparent
                        alpha = Math.Max(0, 255 - (diff - 10) * 4);
                    }
                    
                    // Always write out pure white for the text itself, varying the transparency
                    img.SetPixel(x,y, Color.FromArgb(alpha, 255, 255, 255));
                }
            }
            img.Save(outPath, ImageFormat.Png);
        }
    }
}
"@
Add-Type -TypeDefinition $code -ReferencedAssemblies System.Drawing
[ImageProcessor]::ProcessWhiteOnWhite("public/assets/images/logo_white.jpg", "public/assets/images/logo_white.png")
