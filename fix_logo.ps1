$code = @"
using System;
using System.Drawing;
using System.Drawing.Imaging;

public class ImageProcessor {
    public static void CleanWhiteLogo(string inPath, string outPath) {
        using(Bitmap img = new Bitmap(inPath)) {
            int maxBgLuma = 0;
            for(int y=0; y<5; y++) {
                for(int x=0; x<img.Width; x++) {
                    Color c1 = img.GetPixel(x,y);
                    Color c2 = img.GetPixel(x,img.Height-1-y);
                    int l1 = (int)(0.299*c1.R + 0.587*c1.G + 0.114*c1.B);
                    int l2 = (int)(0.299*c2.R + 0.587*c2.G + 0.114*c2.B);
                    if (l1 > maxBgLuma) maxBgLuma = l1;
                    if (l2 > maxBgLuma) maxBgLuma = l2;
                }
            }
            
            int minLuma = maxBgLuma + 4; 
            int maxLuma = 252;
            if(minLuma >= maxLuma) minLuma = maxLuma - 2;
            
            for(int y=0; y<img.Height; y++) {
                for(int x=0; x<img.Width; x++) {
                    Color p = img.GetPixel(x,y);
                    int luma = (int)(0.299*p.R + 0.587*p.G + 0.114*p.B);
                    
                    int alpha = 0;
                    if(luma >= maxLuma) alpha = 255;
                    else if(luma <= minLuma) alpha = 0;
                    else {
                        float ratio = (float)(luma - minLuma) / (maxLuma - minLuma);
                        // Boost the alpha heavily so the letters look THICK like the screenshot
                        ratio = Math.Min(1.0f, ratio * 1.5f);
                        alpha = (int)(ratio * 255);
                    }
                    
                    img.SetPixel(x,y, Color.FromArgb(alpha, 255, 255, 255));
                }
            }
            img.Save(outPath, ImageFormat.Png);
        }
    }
}
"@
Add-Type -TypeDefinition $code -ReferencedAssemblies System.Drawing
[ImageProcessor]::CleanWhiteLogo("public/assets/images/logo_white.jpg", "public/assets/images/logo_white.png")
