import os
import json

def read_files_in_directory(directory):
    project_data = {}
    
    for root, _, files in os.walk(directory):
        relative_root = os.path.relpath(root, directory)
        if relative_root == ".":
            relative_root = ""

        for file in files:
            file_path = os.path.join(root, file)
            try:
                with open(file_path, "r", encoding="utf-8") as f:
                    content = f.read()
                project_data[os.path.join(relative_root, file)] = content
            except Exception as e:
                project_data[os.path.join(relative_root, file)] = f"Error reading file: {str(e)}"

    return project_data

project_directory = "."  # Pastikan skrip dijalankan dari dalam folder proyek Laravel
project_json = read_files_in_directory(project_directory)

with open("project_laravel.json", "w", encoding="utf-8") as json_file:
    json.dump(project_json, json_file, indent=2, ensure_ascii=False)

print("Proyek Laravel telah dikonversi ke project_laravel.json!")
