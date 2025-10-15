# 🚌 Final Flutter App Development Workflow & AI Agent Guide (Revised Structure)

This document outlines the scope, conventions, and division of labor for building the **Bus Ticketing Application** (Template Phase) using multiple AI agents.

---

## 📝 Agent Work Log

Each agent **must** provide a very short and brief summary of their work here before they start. **Strict adherence to file paths and structure is mandatory to prevent conflicts.**

| Field | Description/Example |
| :--- | :--- |
| **Agent Name/ID** | `Agent-UI-01` |
| **Pages Worked On** | `login_screen.dart`, `registration_screen.dart` |
| **Functionality** | Handles authentication link between login and sign-up. |
| **File Location** | `lib/presentation/login_screen/` |

*(Note: Agents should add their own entries to the table above. The "Export to Sheets" note is for human reference.)*

---

## 🛠️ General Workflow Instructions for All Agents

1.  **STRICTLY READ THIS ENTIRE DOCUMENT FIRST**: Before writing a single line of code, you must read and understand all instructions. You must **carefully review the existing files** (`lib/theme/app_theme.dart`, `lib/widgets/*.dart`) to determine the established color scheme, typography, and widget styling.

2.  **COLLABORATIVE CODING WARNING**: You are one of **multiple AI agents** working on this project. Your primary directive is to **work only on the page specified in your prompt**. Do not modify files outside of your designated task.

3.  **FILE STRUCTURE MANDATE (CRITICAL)**: All pages must be created inside the `lib/presentation/` directory following this strict structure:
    * `lib/presentation/page_folder_name/page_file_name.dart`
    * *(The folder name and the file name must match, e.g., `lib/presentation/home_screen/home_screen.dart`)*

4.  **UI STYLE MANDATE**:
    * Prioritize a **simple, compact, and professional design**.
    * Use **smaller font sizes** and **smaller, well-contained boxes/cards** to maximize content density.
    * **Use existing components** from `lib/widgets/` (e.g., `CustomImageWidget`, `CustomIconWidget`) and the styling defined in **`lib/theme/app_theme.dart`** wherever possible.

5.  **PHASE FOCUS: UI TEMPLATE ONLY**: Your current task is to build the **UI template only**. Do not include any API calls, complex state management, or business logic. Use placeholder/dummy data where needed.