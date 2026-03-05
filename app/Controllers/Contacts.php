<?php

namespace App\Controllers;

class Contacts extends BaseController
{
    private string $csvFile;

    public function __construct()
    {
        $this->csvFile = WRITEPATH . 'contacts.csv';
    }

    /**
     * Read all contacts from CSV file
     */
    private function readContacts(): array
    {
        $contacts = [];
        
        if (file_exists($this->csvFile)) {
            $handle = fopen($this->csvFile, 'r');
            if ($handle) {
                fgetcsv($handle); // Skip header
                while (($row = fgetcsv($handle)) !== false) {
                    $contacts[] = [
                        'id' => count($contacts),
                        'name' => $row[0] ?? '',
                        'phone' => $row[1] ?? '',
                        'email' => $row[2] ?? ''
                    ];
                }
                fclose($handle);
            }
        }
        
        return $contacts;
    }

    /**
     * Save contacts to CSV file
     */
    private function saveContacts(array $contacts): void
    {
        $handle = fopen($this->csvFile, 'w');
        fputcsv($handle, ['Name', 'Phone', 'Email']);
        
        foreach ($contacts as $contact) {
            fputcsv($handle, [
                $contact['name'],
                $contact['phone'],
                $contact['email']
            ]);
        }
        fclose($handle);
    }

    /**
     * Display all contacts (with search capability)
     */
    public function index()
    {
        $contacts = $this->readContacts();
        $searchQuery = $this->request->getGet('search') ?? '';
        
        // Apply search filter
        if (!empty($searchQuery)) {
            $searchQuery = strtolower(trim($searchQuery));
            $contacts = array_filter($contacts, function($contact) use ($searchQuery) {
                return stripos($contact['name'], $searchQuery) !== false ||
                       stripos($contact['phone'], $searchQuery) !== false ||
                       stripos($contact['email'], $searchQuery) !== false;
            });
        }
        
        // Calculate statistics
        $allContacts = $this->readContacts();
        $stats = [
            'total' => count($allContacts),
            'withPhone' => count(array_filter($allContacts, fn($c) => !empty($c['phone']))),
            'withEmail' => count(array_filter($allContacts, fn($c) => !empty($c['email'])))
        ];
        
        return view('contacts/index', [
            'contacts' => $contacts,
            'searchQuery' => $searchQuery,
            'stats' => $stats,
            'resultsCount' => count($contacts)
        ]);
    }

    /**
     * Show form to create new contact
     */
    public function create()
    {
        return view('contacts/create');
    }

    /**
     * Store new contact
     */
    public function store()
    {
        $name = $this->request->getPost('name');
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');
        
        if (empty(trim($name))) {
            return redirect()->back()->with('error', 'El nombre es obligatorio')->withInput();
        }
        
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'El email no es válido')->withInput();
        }
        
        // Create CSV file if doesn't exist
        if (!file_exists($this->csvFile)) {
            $handle = fopen($this->csvFile, 'w');
            fputcsv($handle, ['Name', 'Phone', 'Email']);
            fclose($handle);
        }
        
        // Append new contact
        $handle = fopen($this->csvFile, 'a');
        fputcsv($handle, [trim($name), trim($phone), trim($email)]);
        fclose($handle);
        
        return redirect()->to('/contacts')->with('success', '¡Contacto añadido correctamente!');
    }

    /**
     * Show edit form for a contact
     */
    public function edit($id)
    {
        $contacts = $this->readContacts();
        
        if (!isset($contacts[$id])) {
            return redirect()->to('/contacts')->with('error', 'Contacto no encontrado');
        }
        
        return view('contacts/edit', ['contact' => $contacts[$id]]);
    }

    /**
     * Update a contact
     */
    public function update($id)
    {
        $contacts = $this->readContacts();
        
        if (!isset($contacts[$id])) {
            return redirect()->to('/contacts')->with('error', 'Contacto no encontrado');
        }
        
        $name = $this->request->getPost('name');
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');
        
        if (empty(trim($name))) {
            return redirect()->back()->with('error', 'El nombre es obligatorio')->withInput();
        }
        
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'El email no es válido')->withInput();
        }
        
        $contacts[$id] = [
            'id' => $id,
            'name' => trim($name),
            'phone' => trim($phone),
            'email' => trim($email)
        ];
        
        $this->saveContacts($contacts);
        
        return redirect()->to('/contacts')->with('success', '¡Contacto actualizado correctamente!');
    }

    /**
     * Delete a contact
     */
    public function delete($id)
    {
        $contacts = $this->readContacts();
        
        if (!isset($contacts[$id])) {
            return redirect()->to('/contacts')->with('error', 'Contacto no encontrado');
        }
        
        array_splice($contacts, $id, 1);
        $this->saveContacts($contacts);
        
        return redirect()->to('/contacts')->with('success', '¡Contacto eliminado correctamente!');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('contacts/import');
    }

    /**
     * Handle CSV import
     */
    public function import()
    {
        $file = $this->request->getFile('csv_file');
        
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Error al subir el archivo');
        }
        
        if (!in_array($file->getMimeType(), ['text/csv', 'text/plain', 'application/vnd.ms-excel'])) {
            return redirect()->back()->with('error', 'El archivo debe ser CSV');
        }
        
        $contacts = $this->readContacts();
        $imported = 0;
        $errors = [];
        
        if (($handle = fopen($file->getTempName(), 'r')) !== false) {
            fgetcsv($handle); // Skip header
            $row = 0;
            
            while (($data = fgetcsv($handle)) !== false) {
                $row++;
                
                if (empty($data[0])) {
                    continue;
                }
                
                $name = $data[0] ?? '';
                $phone = $data[1] ?? '';
                $email = $data[2] ?? '';
                
                if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Fila $row: Email inválido";
                    continue;
                }
                
                // Check for duplicates
                $isDuplicate = false;
                foreach ($contacts as $contact) {
                    if (strtolower($contact['name']) === strtolower(trim($name))) {
                        $isDuplicate = true;
                        break;
                    }
                }
                
                if (!$isDuplicate) {
                    $contacts[] = [
                        'id' => count($contacts),
                        'name' => trim($name),
                        'phone' => trim($phone),
                        'email' => trim($email)
                    ];
                    $imported++;
                }
            }
            fclose($handle);
        }
        
        $this->saveContacts($contacts);
        
        $message = "¡Se importaron $imported contactos correctamente!";
        if (!empty($errors)) {
            $message .= " Errores: " . implode(", ", $errors);
        }
        
        return redirect()->to('/contacts')->with('success', $message);
    }

    /**
     * Export contacts to CSV
     */
    public function exportCsv()
    {
        $contacts = $this->readContacts();
        
        if (empty($contacts)) {
            return redirect()->to('/contacts')->with('error', 'No hay contactos para exportar');
        }
        
        $filename = 'contacts_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Name', 'Phone', 'Email']);
        
        foreach ($contacts as $contact) {
            fputcsv($handle, [
                $contact['name'],
                $contact['phone'],
                $contact['email']
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Export single contact to vCard format
     */
    public function exportVcard($id)
    {
        $contacts = $this->readContacts();
        
        if (!isset($contacts[$id])) {
            return redirect()->to('/contacts')->with('error', 'Contacto no encontrado');
        }
        
        $contact = $contacts[$id];
        
        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";
        $vcard .= "FN:" . $contact['name'] . "\r\n";
        
        if (!empty($contact['phone'])) {
            $vcard .= "TEL:" . $contact['phone'] . "\r\n";
        }
        
        if (!empty($contact['email'])) {
            $vcard .= "EMAIL:" . $contact['email'] . "\r\n";
        }
        
        $vcard .= "END:VCARD\r\n";
        
        $filename = preg_replace('/[^a-z0-9_-]/', '_', strtolower($contact['name'])) . '.vcf';
        
        header('Content-Type: text/vcard; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo $vcard;
        exit;
    }
}
