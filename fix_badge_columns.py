import os
import re

directory = 'app/Filament/'

for root, _, files in os.walk(directory):
    for file in files:
        if file.endswith('.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r') as f:
                content = f.read()
            
            original_content = content
            
            # Remove BadgeColumn import if TextColumn is already imported
            if 'use Filament\\Tables\\Columns\\BadgeColumn;' in content and 'use Filament\\Tables\\Columns\\TextColumn;' in content:
                content = content.replace('use Filament\\Tables\\Columns\\BadgeColumn;\n', '')
                content = content.replace('use Filament\\Tables\\Columns\\BadgeColumn;\r\n', '')
                content = content.replace('use Filament\\Tables\\Columns\\BadgeColumn;', '')
            # Or replace it if TextColumn is not imported
            elif 'use Filament\\Tables\\Columns\\BadgeColumn;' in content:
                content = content.replace('use Filament\\Tables\\Columns\\BadgeColumn;', 'use Filament\\Tables\\Columns\\TextColumn;')

            # Replace Tables\Columns\BadgeColumn::make(...) with Tables\Columns\TextColumn::make(...)->badge()
            # Also handle just BadgeColumn::make(...) if it was imported
            
            # Using regex to match BadgeColumn::make('...') and add ->badge()
            # Pattern: BadgeColumn::make(something)
            pattern = r'Tables\\Columns\\BadgeColumn::make\(([^)]+)\)'
            content = re.sub(pattern, r'Tables\\Columns\\TextColumn::make(\1)->badge()', content)
            
            pattern2 = r'(?<!Tables\\Columns\\)BadgeColumn::make\(([^)]+)\)'
            content = re.sub(pattern2, r'TextColumn::make(\1)->badge()', content)
            
            # Replace ->colors( with ->color( BUT only if it was chained on a badge column?
            # It's safer to just replace ->colors( with ->color( in the entire file if we modified BadgeColumn
            if content != original_content:
                content = content.replace('->colors(', '->color(')
            
            if content != original_content:
                with open(filepath, 'w') as f:
                    f.write(content)
                print(f"Fixed {filepath}")

print("Done.")
