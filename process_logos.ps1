$code = @"
using System;
using System.Drawing;
using System.Drawing.Imaging;

public class ImageProcessor {
    public static void ProcessColorful(string inPath, string outPath) {
        using(Bitmap img = new Bitmap(inPath)) {
            Color bg = img.GetPixel(0,0);
            for(int y=0; y<img.Height; y++) {
                for(int x=0; x<img.Width; x++) {
                    Color p = img.GetPixel(x,y);
                    int diff = Math.Max(Math.Abs(p.R - bg.R), Math.Max(Math.Abs(p.G - bg.G), Math.Abs(p.B - bg.B)));
                    if (diff < 10) {
                        // fully transparent
                        img.SetPixel(x,y, Color.Transparent);
                    } else if (diff < 50) {
                        // antialiased edge
                        int alpha = (int)((diff - 10) * 255 / 40.0f);
                        img.SetPixel(x,y, Color.FromArgb(alpha, p.R, p.G, p.B));
                    }
                }
            }
            img.Save(outPath, ImageFormat.Png);
        }
    }
    
    public static void ProcessWhite(string inPath, string outPath) {
        using(Bitmap img = new Bitmap(inPath)) {
            Color bg = img.GetPixel(0,0);
            bool isWhiteBg = (bg.R > 128); 
            for(int y=0; y<img.Height; y++) {
                for(int x=0; x<img.Width; x++) {
                    Color p = img.GetPixel(x,y);
                    int luma = (int)(0.299*p.R + 0.587*p.G + 0.114*p.B);
                    int alpha = isWhiteBg ? Math.Max(0, 255 - luma) : luma;
                    // apply some contrast directly to avoid faint halos
                    alpha = (int)Math.Min(255, Math.Max(0, (alpha - 50) * 1.5f));
                    img.SetPixel(x,y, Color.FromArgb(alpha, 255, 255, 255));
                }
            }
            img.Save(outPath, ImageFormat.Png);
        }
    }
}
"@
Add-Type -TypeDefinition $code -ReferencedAssemblies System.Drawing
[ImageProcessor]::ProcessColorful("public/assets/images/logo_colorful.jpg", "public/assets/images/logo_colorful.png")
[ImageProcessor]::ProcessWhite("public/assets/images/logo_white.jpg", "public/assets/images/logo_white.png")
