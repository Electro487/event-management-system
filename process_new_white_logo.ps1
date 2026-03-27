$code = @"
using System;
using System.Drawing;
using System.Drawing.Imaging;

public class ImageProcessor {
    public static void ProcessWhite(string inPath, string outPath) {
        using(Bitmap img = new Bitmap(inPath)) {
            for(int y=0; y<img.Height; y++) {
                for(int x=0; x<img.Width; x++) {
                    Color p = img.GetPixel(x,y);
                    int luma = (int)(0.299*p.R + 0.587*p.G + 0.114*p.B);
                    // Extract white from dark green
                    // Dark green luma is around 40, white is 255
                    int alpha = (int)Math.Min(255, Math.Max(0, (luma - 60) * 1.5f));
                    img.SetPixel(x,y, Color.FromArgb(alpha, 255, 255, 255));
                }
            }
            img.Save(outPath, ImageFormat.Png);
        }
    }
}
"@
Add-Type -TypeDefinition $code -ReferencedAssemblies System.Drawing
[ImageProcessor]::ProcessWhite("public/assets/images/logo_new_source.png", "public/assets/images/logo_white.png")
